<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\PlayerInfo as PlayerInfoModel;

use app\common\model\querylist\TeamInfo;
use app\common\validate\querylist\PlayerInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class PlayerInfo extends Admin
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
            'q'       => isset($request['q']) ? trim($request['q']) : '',
            'is_intergrated' => isset($request['is_intergrated']) ? $request['is_intergrated'] : 0,
            'game' => $game,
            'original_source' => isset($request['original_source']) ? $request['original_source'] : $default_original_source,
        ];
        $args = [
            'query'  => $query,
            'field'  => '',
            'order'  => 'player_id desc',
            'params' => $query,
            'limit'  => 20,
        ];
        $gameList=config('app.game_type');
        $originalSource=config('app.original_source');
        // 分页列表
        $playInfoModel=new PlayerInfoModel();
        $list=$playInfoModel->getList($args);

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
            $validate = new PlayerInfoValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            if(isset($request['aka']) && $request['aka']){

                if(strpos($request['aka'],'，') !==false){
                    $request['aka']=str_replace('，',',',$request['aka']);
                }
                $request['aka']=explode(',',$request['aka']);

                $request['aka']=json_encode($request['aka']);
            }
            //反编译元字符
            $request['team_history'] = htmlspecialchars_decode($request['team_history']);
            $request['event_history'] = htmlspecialchars_decode($request['event_history']);
          


            //需要加事务
            Db::startTrans();
            try {
                $playerInfoObj= new PlayerInfoModel();
                $changeLog = ChangeLogs::checkInsertData('app\common\model\querylist\PlayerInfo', $request, $request['player_id'], 'player', $this->username, 'player_id');
                if ($changeLog) {
                    $request['update_time']=date("Y-m-d H:i:s");
                    $playerInfoObj->isUpdate(true)->allowField(true)->save($request);

                    if (is_numeric($playerInfoObj->player_id)) {
                        $postData=['params'=>json_encode([$playerInfoObj->player_id]),'dataType' =>'totalPlayerInfo'];
                        $api_host=config('app.api_host').'/refresh';
                        $return=curl_post($api_host, $postData);
                        if($playerInfoObj->pid >0)  {//当tid>0时更新缓存
                            $intergratedPostData=['params'=>json_encode([$playerInfoObj->pid]),'dataType' =>'intergratedPlayer'];
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
        $info = PlayerInfoModel::get($id);
        $info['aka']=json_decode($info['aka'],true);
        if(is_array($info['aka'])){
            if(!empty($info['aka'])){
                $info['aka']=implode(',',$info['aka']);
            }else{
                $info['aka']='';
            }
        }else{
            $info['aka']='';
        }


        $info['event_history']=!empty($info['event_history']) ?$info['event_history']: '';
        $teamModel=new TeamInfo();
        $map['game']=$info['game'];
        $teamList=$teamModel->teamList($map);
        $typeList=config('app.game_type');

        $data = [
            'info'  => $info,
            'teamList'  => $teamList,
            'typeList'=>$typeList
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = PlayerInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new PlayerInfoModel();
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
        $playerInfoObj = new PlayerInfoModel();
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new PlayerInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['site_id']=$request['site_id']?? 0;
            $map['site_id']=$request['site_id'];
            $map['game']=$request['game'];
            $map['original_source']=$request['original_source'];
            $checks=$playerInfoObj->getFieldList($map,'player_id',$orderBy='player_id');
            if(count($checks)>0){
                return $this->response(201, '该站点的队员已经存在');
            }

            if(isset($request['aka']) && $request['aka']){
                if(strpos($request['aka'],'，') !==false){
                    $request['aka']=str_replace('，',',',$request['aka']);
                }
                $request['aka']=explode(',',$request['aka']);
                $request['aka']=json_encode($request['aka']);
            }
            $request['create_time']=date("Y-m-d H:i:s");
            $request['update_time']=date("Y-m-d H:i:s");
            $request['team_history']=json_encode([]);
            $request['event_history']=json_encode([]);
            $request['stat'] = json_encode([]);
            $request['player_stat'] = json_encode([]);


            
            $playerInfoObj->allowField(true)->save($request);

            if (is_numeric($playerInfoObj->player_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $typeList=config('app.game_type');
        $originalSource=config('app.original_source');

        return $this->fetch('create',['typeList'=>$typeList,'originalSource'=>$originalSource]);
    }

    //根据选择游戏获取战队信息
    public function getTeamList(){
        $game = Request::param('game');
        $teamModel=new TeamInfo();
        $map['game']=$game;
        $teamList=$teamModel->teamList($map);
        $strHtml='<option value="">--请选择--</option>';

        if(count($teamList)>0){
            foreach ($teamList as $key=>$val){
                $tip=(isset($val['tid']) && $val['tid']>0) ?"已整合":"未整合";
                $strHtml.='<option value="'.$val['team_id'].'">'.$val['team_name'].'(tid:'.$val['tid'].'-'.$tip.')</option>';
            }
        }
        return $this->response(200, Lang::get('Success'),$strHtml);
    }

    public function selectHmtl()
    {
        $player_id = Request::param('player_id',0);
        $pid = Request::param('pid',0);
        $team_id = Request::param('team_id',0);
        //$type:1表示并入，2.表示已经合并，3表示两天tid>0的合并，4表示两个未合并的合并
        $type = Request::param('type','');
        if($type !=''){
            switch($type)
            {
                case "getUnmergedPlayerList":
                    //获取两个未整合的队员列表
                    $playerInfoModel=new PlayerInfoModel();
                    $game = Request::param('game',0);
                    //==============获取存在的战队id==============
                    $teamInfoModel=new TeamInfo();
                    $teamInfo=$teamInfoModel->getTeamInfoByTeamId($team_id);
                    if(isset($teamInfo['tid']) && $teamInfo['tid']>0){
                        $teamWhere['tid']=$teamInfo['tid'] ?? 0;
                        $teamIds=$teamInfoModel->getIds($teamWhere,$field='team_id');
                        //==============获取存在的战队id==============
                        //==============获取所有pid=0,team_id存在的所有队员==============
                        $map['pid']=0;
                        $map['game']=$game;
                        $playerInfos=$playerInfoModel->getFieldList($map,'player_name,team_id','player_name',$teamIds);//排序

                        $playerInfos=$playerInfos ?? [];
                        //==============获取所有pid=0,team_id存在的所有队员==============
                        $html = '<select class="filed_select" style="width:180px;height: 30px;line-height: 30px;margin: 0 auto;" name="player_id"><option value="0">请选择</option>';
                        if (count($playerInfos) > 0) {
                            foreach ($playerInfos as $key => $val) {
                                if($key!=$player_id && $val != ''){
                                    $html .= '<option value="' . $key. '">' . $val['player_name'].'/'. $val['team_id'] .'</option>';
                                }
                            }
                        }else{
                            $html.='<option value="0">队员数据为空</option>';
                        }
                        $html .= '</select>';
                        $html .= '<input type="hidden" name="player_id" value="' . $player_id . '">';
                        $html .= '<input type="hidden" name="type" value="merge2unmergedPlayer">';

                    }else{
                        $html='未整合的队伍中的队员不做整合操作（team_id:'.$team_id.'）';
                    }

                    break;
                case "getMergedPlayerList4pid":
                    //==================获取所有整合过的队员列表======================
                    $postData = json_encode(['team_id'=>$team_id, 'type' => 'playerList_team_id','pageSize'=>1000]);
                    $api_host = config('app.api_host') . '/getIntergration';
                    $return = curl_post($api_host, $postData);
                    $playerInfos = json_decode($return, true);
                    //==================获取所有整合过的队员列表======================
                    $playerInfos=$playerInfos ?? [];
                    $html = '<select class="filed_select" style="width:180px;height: 30px;line-height: 30px;margin: 0 auto;" name="pid">';
                    if (count($playerInfos) > 0) {
                        $html .='<option value="0">请选择</option>';
                        array_multisort(array_column($playerInfos,"player_name"),SORT_DESC,$playerInfos);

                        foreach ($playerInfos as $key => $val) {
                            $val['pid']=isset($val['pid']) ? $val['pid']:0;
                            if($val['pid']>0 &&  $val['pid']!=$pid && $val['player_name'] != ''){
                                $html .= '<option value="' . $val['pid']. '">' . $val['player_name'] . '  （'.count($val['intergrated_id_list']).'）</option>';
                            }
                        }
                    }else{
                        $html.='<option value="0">战队数据为空</option>';
                    }
                    $html .= '</select>';
                    $html .= '<input type="hidden" name="newpid"  value="' . $pid . '">';
                    $html .= '<input type="hidden" name="type" value="merge2mergedPlayer">';

                    break;
                case "getMergedPlayerList4playerid":
                    //==================通过接口获取所有整合过的队员列表======================
                    $postData = json_encode(['team_id'=>$team_id, 'type' => 'playerList_team_id','pageSize'=>1000]);
                    $api_host = config('app.api_host') . '/getIntergration';
                    $return = curl_post($api_host, $postData);
                    $playerInfos = json_decode($return, true);
                    $playerInfos=$playerInfos ?? [];

                    if (count($playerInfos) > 0) {
                        $html = '<select class="filed_select" style="width:180px;height: 30px;line-height: 30px;margin: 0 auto;" name="pid">';
                        $html .='<option value="0">请选择</option>';
                        array_multisort(array_column($playerInfos,"player_name"),SORT_DESC,$playerInfos);

                        foreach ($playerInfos as $key => $val) {
                            $val['pid']=isset($val['pid']) ? $val['pid']:0;
                            if($val['pid']>0 &&  $val['pid']!=$pid ){
                                $html .= '<option value="' . $val['pid']. '">' . $val['player_name'] . '  （'.count($val['intergrated_id_list']).'）</option>';
                            }
                        }
                        $html .= '<input type="hidden" name="player_id" value="' . $player_id . '">';
                        $html .= '<input type="hidden" name="type" value="mergePlayer2mergedPlayer">';
                    }else{
                        $html='战队数据为空（team_id:）'.$team_id;
                    }

                    break;

            }
        }

        return $this->response(200, Lang::get('Success'), $html);

    }


    //保存
    public function updataInfo()
    {
        $request = Request::param();
        $type=$request['type'] ?? '';
        if($type !=''){
            switch($type)
            {
                case "merge2mergedPlayer":
                    $pid_1= $request['pid_1'] ?? 0;
                    $pid_2= $request['pid_2'] ?? 0;
                    $postData = json_encode(['pid_1' => $pid_1,'pid_2' => $pid_2,'type' => 'merge2mergedPlayer']);
                    $updataCache=json_encode([
                        'intergratedPlayer_1'=>[$pid_1,"dataType"=>"intergratedPlayer","reset"=>1],
                        'intergratedPlayer_2'=>[$pid_2,"dataType"=>"intergratedPlayer","reset"=>1],
                    ]);


                    break;
                case "merge2unmergedPlayer":
                    $player_id_1= $request['player_id_1'] ?? 0;
                    $player_id_2= $request['player_id_2'] ?? 0;
                    $postData = json_encode(['player_id_1' => $player_id_1,'player_id_2' => $player_id_2,'type' => 'merge2unmergedPlayer']);
                    $updataCache=json_encode([
                        'playerInfo_1'=>[$player_id_1,"dataType"=>"totalPlayerInfo","reset"=>1],
                        'playerInfo_2'=>[$player_id_2,"dataType"=>"totalPlayerInfo","reset"=>1],
                    ]);

                    break;
                case "mergePlayer2mergedPlayer":
                    $pid= $request['pid'] ?? 0;
                    $player_id= $request['player_id'] ?? 0;
                    //合并到已整合的战队
                    $postData = json_encode(['pid' => $pid,'player_id' => $player_id,'type' => 'mergePlayer2mergedPlayer']);
                    $updataCache=json_encode([
                        'playerInfo'=>[$player_id,"dataType"=>"totalPlayerInfo","reset"=>1],
                        'intergratedPlayer'=>[$pid,"dataType"=>"intergratedPlayer","reset"=>1],
                    ]);

                    break;

            }

            $api_host = config('app.api_host') . '/intergration';
            $return = curl_post($api_host, $postData);
            $return=json_decode($return,true);
            $msg=isset($return['log']) ? join("\n",$return['log']):'';
            if ($return['result']) {
                $update_cache_api = config('app.api_host') . '/get';
               // $updataCacheResult = curl_post($update_cache_api, $updataCache);
                //$updataCacheResult=json_decode($updataCacheResult,true);
                return $this->response(200, $msg);
            } else {
                return $this->response(201, $msg);
            }
        }else{
            return $this->response(201, '参数错误');
        }

    }



}