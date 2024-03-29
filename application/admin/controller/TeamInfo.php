<?php

namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\ScoreggMatchList as ScoreggMatchListModel;
use app\common\model\querylist\ShangniuMatchList as ShangniuMatchListModel;
use app\common\model\querylist\TeamInfo as TeamInfoModel;
use app\common\model\querylist\TeamList;
use app\common\validate\querylist\CpseoTeamInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class TeamInfo extends Admin
{
    public function index()
    {
        $request = Request::param();

        $game= isset($request['game']) ? $request['game'] : 'lol';
        //根据游戏不同获取默认来源
        if($game=='lol' || $game=='kpl'){
            $default_original_source="scoregg";
        }elseif($game=='dota2'){
            $default_original_source="shangniu";
        }


        $query = [
            'q' => isset($request['q']) ? trim($request['q']) : '',
            'is_intergrated' => isset($request['is_intergrated']) ? $request['is_intergrated'] : 0,
            'game' => $game,
            'original_source' => isset($request['original_source']) ? $request['original_source'] : $default_original_source,
        ];
        $args = [
            'query' => $query,
            'field' => '',
            'order' => 'team_id desc',
            'params' => $query,
            'limit' => 20,
        ];
        $gameList = config('app.game_type');
        $originalSource = config('app.original_source');
        // 分页列表
        $teamInfoModel = new TeamInfoModel();
        $list = $teamInfoModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        $this->assign('gameList', $gameList);
        $this->assign('originalSource', $originalSource);

        return $this->fetch('index');
    }


    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new CpseoTeamInfoValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            if (isset($request['aka']) && $request['aka']) {
                if (strpos($request['aka'], '，') !== false) {
                    $request['aka'] = str_replace('，', ',', $request['aka']);
                }
                $request['aka'] = explode(',', $request['aka']);
                $request['aka'] = json_encode($request['aka']);
            }
            $race_stat=[];
            if(isset($request['race_stat'])){
                $request['race_stat']=str_replace(array(',',"，"),',',$request['race_stat']);
                $race_stat_temp=explode(',',$request['race_stat']);
                $race_stat_temp=$race_stat_temp ?? [];
                if(count($race_stat_temp) > 0){
                    $race_stat=[
                        'win'=>isset($race_stat_temp[0]) ? intval(str_replace('win:','',$race_stat_temp[0])) :0,
                        'draw'=>isset($race_stat_temp[1]) ? intval(str_replace('draw:','',$race_stat_temp[1])) :0,
                        'lose'=>isset($race_stat_temp[2]) ? intval(str_replace('lose:','',$race_stat_temp[2])) :0,
                    ];
                }

            }

            $request['race_stat'] = json_encode($race_stat);
            //需要加事务
            Db::startTrans();
            try {
                $teamInfoObj = new teamInfoModel();
                $changeLog = ChangeLogs::checkInsertData('app\common\model\querylist\teamInfo', $request, $request['team_id'], 'team', $this->username, 'team_id');
                if ($changeLog) {
                    $request['update_time']=date("Y-m-d H:i:s");
                    //反编译元字符
                    $request['team_history'] = strip_tags(htmlspecialchars_decode($request['team_history']));
                    $request['honor_list'] = strip_tags(htmlspecialchars_decode($request['honor_list']));
                    $request['description'] = htmlspecialchars_decode($request['description']);

                    $rt = $teamInfoObj->isUpdate(true)->allowField(true)->save($request);
                    if (is_numeric($teamInfoObj->team_id)) {
                        $postData=['params'=>json_encode([$teamInfoObj->team_id]),'dataType' =>'totalTeamInfo'];
                        $api_host=config('app.api_host').'/refresh';
                        $return=curl_post($api_host, $postData);
                        if($teamInfoObj->tid >0)  {//当tid>0时更新缓存
                            $intergratedPostData=['params'=>json_encode([$teamInfoObj->tid]),'dataType' =>'intergratedTeam'];
                            $intergratedReturn=curl_post($api_host, $intergratedPostData);
                        }
                        // 提交事务
                        Db::commit();
                        return $this->response(200, Lang::get('Success'));
                    } else {
                        // 回滚事务
                        Db::rollback();
                        return $this->response(201, Lang::get('Fail'));
                    }
                } else {
                    // 回滚事务
                    Db::rollback();
                }

            } catch (\Exception $e) {
                dump($e->getMessage());
                // 回滚事务
                Db::rollback();
            }


        }

        $id = Request::param('id');
        $info = teamInfoModel::get($id);
        $typeList = config('app.game_type');
        $info['aka'] = json_decode($info['aka'], true);

        if(substr($info['race_stat'],0,1)=='"' && substr($info['race_stat'],-1)=='"'){
            $info['race_stat'] = json_decode($info['race_stat'], true);
        }
        $info['race_stat']=json_decode($info['race_stat'],true);
        $race_stat='';
        if(is_array($info['race_stat']) && count($info['race_stat'])>0){
            $race_stat='win:'.$info['race_stat']['win'].',draw:'.$info['race_stat']['draw'].',lose:'.$info['race_stat']['lose'];
        }
        $info['race_stat']=$race_stat;
        if(is_array($info['aka']) && count($info['aka'])>0){
            $info['aka'] = implode(',', $info['aka']);
        }else{
            $info['aka'] = '';
        }
        $playerModel=new \app\common\model\querylist\PlayerInfo();
        $map['team_id']=$id;
        $map['game']=$info['game'];
        $map['original_source']=$info['original_source'];
        $map['status']=1;
        $playerInfoShow=count($playerModel->getFieldList($map,'player_id'));
        $hideMap=$map;
        $hideMap['status']=0;
        $playerInfoHide=count($playerModel->getFieldList($hideMap,'player_id'));
        if($info['original_source']=='shangniu'){
            $matchListModel = new ShangniuMatchListModel();
        }else{
            $matchListModel = new ScoreggMatchListModel();
        }
        $matchMap['home_id|away_id']=$info['site_id'];
        $matchMap['game']=$info['game'];
        $matchCount=$matchListModel->MatchCount($matchMap);
        $playerTotalCount=$playerInfoShow+$playerInfoHide;

        $data = [
            'info' => $info,
            'typeList' => $typeList,
            'playerInfoShow' => $playerInfoShow,
            'playerInfoHide' => $playerInfoHide,
            'playerTotalCount'=>$playerTotalCount,
            'matchCount'=>$matchCount
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = teamInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new teamInfoModel;
        switch ($request['type']) {
            case 'delete':
                foreach ($request['ids'] as $v) {
                    $result = $obj::destroy($v);
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
            case 'approval':
                foreach ($request['ids'] as $v) {
                    $result = $obj
                        ->where('id', $v)
                        ->setField('status', 1);
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
            case 'progress':
                foreach ($request['ids'] as $v) {
                    $result = $obj
                        ->where('id', $v)
                        ->setField('status', 0);
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
        }

        return $this->response(201, Lang::get('Fail'));
    }


    public function create()
    {
        $teamInfoObj = new teamInfoModel();
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new CpseoTeamInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['site_id'] = $request['site_id']??0;
            $map['site_id']=$request['site_id'];
            $map['game']=$request['game'];
            $map['original_source']=$request['original_source'];
            //$checks=$teamInfoObj->getFieldList($map,'team_id',$orderBy='team_id');
            //if(count($checks)>0){
            //    return $this->response(201, '该站点的战队已经存在');
            //}

            $request['honor_list'] = json_encode([]);
            $request['team_history'] = json_encode([]);
            $request['team_stat'] = json_encode([]);
            $request['race_stat'] = json_encode([]);

            if (isset($request['aka']) && $request['aka']) {
                if (strpos($request['aka'], '，') !== false) {
                    $request['aka'] = str_replace('，', ',', $request['aka']);
                }
                $request['aka'] = explode(',', $request['aka']);
                $request['aka'] = json_encode($request['aka']);
            }
            $request['create_time']=date("Y-m-d H:i:s");
            $request['update_time']=date("Y-m-d H:i:s");


            $teamInfoObj->allowField(true)->save($request);

            if (is_numeric($teamInfoObj->team_id)) {
                $postData=['game'=>$request['game'],'site_id' =>$request['site_id'],'type'=>'team'];
                $api_host=config('app.api_host').'/createMission';
                $return=curl_post($api_host, $postData);
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $typeList = config('app.game_type');
        $originalSource = config('app.original_source');

        return $this->fetch('create', ['typeList' => $typeList,'originalSource'=>$originalSource]);
    }

    public function selectHmtl()
    {
        $team_id = Request::param('team_id',0);
        $tid = Request::param('tid',0);
        $game = Request::param('game');
        //$type:1表示并入，2.表示已经合并，3表示两天tid>0的合并，4表示两个未合并的合并
        $type = Request::param('type',1);
        if($type !=4){
            $teamInfos = [];
            $postData = json_encode(['game' => $game, 'type' => 'teamList','pageSize'=>1000]);
            $api_host = config('app.api_host') . '/getIntergration';
            $return = curl_post($api_host, $postData);
            $teamInfos = json_decode($return, true);
            array_multisort(array_column($teamInfos,"team_name"),SORT_DESC,$teamInfos);
			$teamInfos=$teamInfos ?? [];
            $html = '<select class="filed_select" style="width:180px;height: 30px;line-height: 30px;margin: 0 auto;" name="tid"><option value="">请选择</option>';
            if (count($teamInfos) > 0) {
                foreach ($teamInfos as $key => $val) {
                    $val['tid']=isset($val['tid']) ? $val['tid']:0;
                    if($val['tid']>0 &&  $val['tid']!=$tid && $val['team_name'] != ''){
                        $html .= '<option value="' . $val['tid'] . '">' . $val['team_name'] . '  （'.count($val['intergrated_id_list']).'）</option>';
                    }
                }
            }
            $html .= '</select>';
            if($type==3){
                $html .= '<input type="hidden" name="tid" value="' . $tid . '">';
            }else{
                $html .= '<input type="hidden" name="team_id" value="' . $team_id . '">';
            }
        }else{
            $teamInfoModel=new TeamInfoModel();
            $map['tid']=0;
            $map['game']=$game;
            $teamInfos=$teamInfoModel->getFieldList($map,'team_name','team_name');//排序
            $teamInfos=$teamInfos ?? [];
            $html = '<select class="filed_select" style="width:180px;height: 30px;line-height: 30px;margin: 0 auto;" name="team_id"><option value="">请选择</option>';
            if (count($teamInfos) > 0) {
                foreach ($teamInfos as $key => $val) {
                    if($key!=$team_id && $val != ''){
                        $html .= '<option value="' . $key. '">' . $val .'</option>';
                    }
                }
            }
            $html .= '</select>';
            $html .= '<input type="hidden" name="team_id" value="' . $team_id . '">';
        }

        $html .= '<input type="hidden" name="type" value="' . $type . '">';
        return $this->response(200, Lang::get('Success'), $html);

    }

    public function updataInfo()
    {
        $request = Request::param();
        $team_id=$request['team_id'] ?? 0;//战队id,隐藏域传值
        $tid_2=$request['tid_2'] ?? 0;//选择的tid
        $tid=$request['tid'] ?? 0;//战队tid,隐藏域传值
        $team_id_2=$request['team_id_2'] ?? 0; //下拉选择战队team_id
        $type=$request['type'] ?? 1;  //$type:1表示并入，2.表示已经合并，3表示两天tid>0的合并，4表示两个未合并的合并

        if ($tid > 0 || $team_id_2 || $tid_2 || $team_id) {
            if($type==1){
                //合并到已整合的战队
                $postData = json_encode(['tid' => $tid,'team_id' => $team_id,'type' => 'mergeTeam2mergedTeam']);
                $updataCache=json_encode([
                    'teamInfo'=>[$team_id,"dataType"=>"totalTeamInfo","reset"=>1],
                    'intergratedTeam'=>[$tid,"dataType"=>"intergratedTeam","reset"=>1],
                ]);
            }elseif($type==3){
                //已整合的两个战队合并（team_list）
                $postData = json_encode(['tid_1' => $tid,'tid_2' => $tid_2,'type' => 'merge2mergedTeam']);
                $updataCache=json_encode([
                    'intergratedTeam_1'=>[$tid,"dataType"=>"intergratedTeam","reset"=>1],
                    'intergratedTeam_2'=>[$tid_2,"dataType"=>"intergratedTeam","reset"=>1],
                ]);

            }elseif($type==4){
                //两个未合并的战队合并
                $postData = json_encode(['team_id_1' => $team_id,'team_id_2' => $team_id_2,'type' => 'merge2unmergedTeam']);
                $updataCache=json_encode([
                    'teamInfo_1'=>[$team_id,"dataType"=>"totalTeamInfo","reset"=>1],
                    'teamInfo_2'=>[$team_id_2,"dataType"=>"totalTeamInfo","reset"=>1],
                ]);

            }elseif($type==5){
                //自我整合
                $postData = json_encode(['team_id' => $team_id,'type' => 'merge1unmergedTeam']);
                $updataCache=json_encode([
                    'teamInfo'=>[$team_id,"dataType"=>"totalTeamInfo","reset"=>1],
                ]);


            }


            $api_host = config('app.api_host') . '/intergration';
            $return = curl_post($api_host, $postData);
            $return=json_decode($return,true);
            $msg=isset($return['log']) ? join("\n",$return['log']):'';
            if ($return['result']) {
                $update_cache_api = config('app.api_host') . '/get';
                $updataCacheResult = curl_post($update_cache_api, $updataCache);
                $updataCacheResult=json_decode($updataCacheResult,true);
                return $this->response(200, $msg,$updataCacheResult);
            } else {
                return $this->response(201, $msg);
            }
        } else {
            return $this->response(201, '参数错误');
        }

    }


}