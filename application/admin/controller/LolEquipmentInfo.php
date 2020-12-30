<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\LolEquipmentInfo as LolEquipmentInfoModel;

use app\common\validate\querylist\LolEquipmentInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class LolEquipmentInfo extends Admin
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
        $lolEquipmentModel=new LolEquipmentInfoModel();
        $lolEquipmentData=$lolEquipmentModel->getList($params,$q);
        $new_data=[];
        if (isset($lolEquipmentData)) {
            foreach ($lolEquipmentData as $v) {
                array_push($new_data, $v);
            }
        }
        $this->assign('search', $search);
        $this->assign('list', $new_data);
        $this->assign('page', $lolEquipmentData->render());

        return $this->fetch('index');
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new LolEquipmentInfoValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['update_time']=date("Y-m-d H:i:s");
            $lolEquipmentObj= new LolEquipmentInfoModel();
            $lolEquipmentObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($lolEquipmentObj->equipment_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = LolEquipmentInfoModel::get($id);
        $roleArray=$intoList=[];
        if($info && $info['maps']){
            $roleArray=json_decode($info['maps'],true);
            $roleArray=array_filter($roleArray);
        }
        if($info && $info['into_list']){
            $intoList=json_decode($info['into_list'],true);
        }

        $data = [
            'info'  => $info,
           // 'role_list'=>$role_list,
            'roleArray'=>$roleArray
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = LolEquipmentInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new LolEquipmentInfoModel();
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
            $validate = new LolEquipmentInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['create_time']=date("Y-m-d H:i:s");
            $request['update_time']=date("Y-m-d H:i:s");

            $lolEquipmentObj = new LolEquipmentInfoModel();
            $lolEquipmentObj->allowField(true)->save($request);

            if (is_numeric($lolEquipmentObj->equipment_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        //$role_list=$this->role_list;

        return $this->fetch('create');
    }



}