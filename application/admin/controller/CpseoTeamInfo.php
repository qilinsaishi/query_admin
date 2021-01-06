<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\CpseoTeamInfo as CpseoTeamInfoModel;
use app\common\validate\querylist\CpseoTeamInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class CpseoTeamInfo extends Admin
{
    public function index()
    {
        $request = Request::param();
        $query = [
            'q'       => isset($request['q']) ? $request['q'] : '',
            'game'  => isset($request['game']) ? $request['game'] : '',
        ];
        $args = [
            'query'  => $query,
            'field'  => '',
            'order'  => 'team_id desc',
            'params' => $query,
            'limit'  => 20,
        ];
        $gameList=config('app.game_type');
        // 分页列表
        $seoTeamModel=new CpseoTeamInfoModel();
        $list = $seoTeamModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        $this->assign('gameList', $gameList);

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
            if(isset($request['aka']) && $request['aka']){
                if(strpos($request['aka'],'，') !==false){
                    $request['aka']=str_replace('，',',',$request['aka']);
                }
                $request['aka']=explode(',',$request['aka']);
                $request['aka']=json_encode($request['aka']);
            }
            $cpseoTeamInfoObj= new CpseoTeamInfoModel();
            $cpseoTeamInfoObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($cpseoTeamInfoObj->team_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = CpseoTeamInfoModel::get($id);
        $typeList=config('app.game_type');
        $info['aka']=json_decode($info['aka'],true);
        $info['race_stat']=json_decode($info['race_stat'],true);
        if(!empty($info['aka'])){
            $info['aka']=implode(',',$info['aka']);
        }else{
            $info['aka']='';
        }
        /*if(!empty($info['race_stat'])){
            $info['race_stat']=implode(',',$info['race_stat']);
        }else{
            $info['race_stat']='';
        }*/

        $data = [
            'info'  => $info,
            'typeList'=>$typeList
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = CpseoTeamInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new CpseoTeamInfoModel;
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
            $validate = new CpseoTeamInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['site_id']=$this->site_id;
            if(isset($request['aka']) && $request['aka']){
                if(strpos($request['aka'],'，') !==false){
                    $request['aka']=str_replace('，',',',$request['aka']);
                }
                $request['aka']=explode(',',$request['aka']);
                $request['aka']=json_encode($request['aka']);
            }

            $cpseoTeamInfoObj = new CpseoTeamInfoModel();
            $cpseoTeamInfoObj->allowField(true)->save($request);

            if (is_numeric($cpseoTeamInfoObj->team_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $typeList=config('app.game_type');
        //$typeList=config('app.config_type');

        return $this->fetch('create',['typeList'=>$typeList]);
    }



}