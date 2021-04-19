<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\CpseoTeamInfo as CpseoTeamInfoModel;
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
        $query = [
            'q'       => isset($request['q']) ? $request['q'] : '',
            'game'  => isset($request['game']) ? $request['game'] : '',
            'original_source'  => isset($request['original_source']) ? $request['original_source'] : '',
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
            $request['team_history']=$request['team_history'];
            $request['event_history']=$request['event_history'];

            //需要加事务
            Db::startTrans();
            try {
                $playerInfoObj= new PlayerInfoModel();
                $changeLog = ChangeLogs::checkInsertData('app\common\model\querylist\PlayerInfo', $request, $request['player_id'], 'player', $this->username, 'player_id');
                if ($changeLog) {
                    $playerInfoObj->isUpdate(true)->allowField(true)->save($request);
                    if (is_numeric($playerInfoObj->player_id)) {
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
        if(!empty($info['aka'])){
            $info['aka']=implode(',',$info['aka']);
        }else{
            $info['aka']='';
        }
        /*if($info['team_history']=='[]'){
            $info['team_history']='';
        }
        if($info['event_history']=='[]'){
            $info['event_history']='';
        }*/


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
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new PlayerInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['site_id']=$this->site_id;
            $request['stat']='';
            if(isset($request['aka']) && $request['aka']){
                if(strpos($request['aka'],'，') !==false){
                    $request['aka']=str_replace('，',',',$request['aka']);
                }
                $request['aka']=explode(',',$request['aka']);
                $request['aka']=json_encode($request['aka']);
            }
            $request['team_history']=addslashes($request['team_history']);
            $request['event_history']=addslashes($request['event_history']);
            $playerInfoObj = new PlayerInfoModel();
            $playerInfoObj->allowField(true)->save($request);

            if (is_numeric($playerInfoObj->player_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $typeList=config('app.game_type');

        return $this->fetch('create',['typeList'=>$typeList]);
    }

    public function getTeamList(){
        $game = Request::param('game');
        $teamModel=new TeamInfo();
        $map['game']=$game;
        $teamList=$teamModel->teamList($map);
        $strHtml='<option value="">--请选择--</option>';

        if(count($teamList)>0){
            foreach ($teamList as $key=>$val){
                $strHtml.='<option value="'.$val['team_id'].'">'.$val['team_name'].'</option>';
            }
        }
        return $this->response(200, Lang::get('Success'),$strHtml);
    }



}