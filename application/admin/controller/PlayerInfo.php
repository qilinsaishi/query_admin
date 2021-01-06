<?php
namespace app\admin\controller;

use app\common\model\Collect;
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
        $params = [];
        $search = [];
        if (isset($request['q'])) {
            $q           = $request['q'];
            $params['q'] = $request['q'];
            $search['q'] = $request['q'];
        }else {
            $q = '';
            $search['q'] = '';
        }
        $playInfoModel=new PlayerInfoModel();
        $playInfoData=$playInfoModel->getList($params,$q);
        $new_data=[];
        if (isset($playInfoData)) {
            foreach ($playInfoData as $v) {
                array_push($new_data, $v);
            }
        }
        $this->assign('search', $search);
        $this->assign('list', $new_data);
        $this->assign('page', $playInfoData->render());

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
            $request['team_history']=addslashes($request['team_history']);
            $request['event_history']=addslashes($request['event_history']);

            $playerInfoObj= new PlayerInfoModel();
            $playerInfoObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($playerInfoObj->player_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
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
        $teamList=$teamModel->teamList('lol');
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
        $teamModel=new TeamInfo();
        $teamList=$teamModel->teamList('lol');
        $typeList=config('app.game_type');

        return $this->fetch('create',['typeList'=>$typeList,'teamList'=>$teamList]);
    }



}