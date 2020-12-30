<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\LolSummonerInfo as LolSummonerInfoModel;

use app\common\validate\querylist\LolSummonerInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class LolSummonerInfo extends Admin
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
        $lolSummonerInfoModel=new LolSummonerInfoModel();
        $lolSummonerInfoModel=$lolSummonerInfoModel->getList($params,$q);
        $new_data=[];
        if (isset($lolSummonerInfoModel)) {
            foreach ($lolSummonerInfoModel as $v) {
                array_push($new_data, $v);
            }
        }
        $this->assign('search', $search);
        $this->assign('list', $new_data);
        $this->assign('page', $lolSummonerInfoModel->render());

        return $this->fetch('index');
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new LolSummonerInfoValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['update_time']=date("Y-m-d H:i:s");
            $lolSummonerInfoObj= new LolSummonerInfoModel();
            $lolSummonerInfoObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($lolSummonerInfoObj->skill_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = LolSummonerInfoModel::get($id);
        $roleArray=$intoList=[];

       /* if($info && $info['into_list']){
            $intoList=json_decode($info['into_list'],true);
        }*/

        $data = [
            'info'  => $info,
           // 'role_list'=>$role_list,
           // 'roleArray'=>$roleArray
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = LolSummonerInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new LolSummonerInfoModel();
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
            $validate = new LolSummonerInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['create_time']=date("Y-m-d H:i:s");
            $request['update_time']=date("Y-m-d H:i:s");

            $lolSummonerInfoObj = new LolSummonerInfoModel();
            $lolSummonerInfoObj->allowField(true)->save($request);

            if (is_numeric($lolSummonerInfoObj->skill_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        //$role_list=$this->role_list;

        return $this->fetch('create');
    }



}