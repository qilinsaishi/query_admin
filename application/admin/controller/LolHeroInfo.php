<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\LolHeroInfo as LolHeroInfoModel;

use app\common\validate\querylist\LolHeroInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class LolHeroInfo extends Admin
{
    //职业列表
    protected $role_list =
        [
            "fighter"=>"战士",
            "mage"=>"法师",
            "assassin"=>"刺客",
            "tank"=>"坦克",
            "marksman"=>"射手",
            "support"=>"辅助"
        ];
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
        $playInfoModel=new LolHeroInfoModel();
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
            $validate = new LolHeroInfoValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['roles']=json_encode($request['roles']);
            $request['update_time']=date("Y-m-d H:i:s");
            $heroInfoObj= new LolHeroInfoModel();
            $heroInfoObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($heroInfoObj->hero_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = LolHeroInfoModel::get($id);
        $roleArray=[];
        if($info && $info['roles']){
            $roleArray=json_decode($info['roles'],true);
        }
        $role_list=$this->role_list;

        $data = [
            'info'  => $info,
            'role_list'=>$role_list,
            'roleArray'=>$roleArray
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = LolHeroInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new LolHeroInfoModel();
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
            $validate = new LolHeroInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['create_time']=date("Y-m-d H:i:s");
            $request['update_time']=date("Y-m-d H:i:s");

            $heroInfoObj = new LolHeroInfoModel();
            $heroInfoObj->allowField(true)->save($request);

            if (is_numeric($heroInfoObj->hero_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $role_list=$this->role_list;

        return $this->fetch('create',['role_list'=>$role_list]);
    }



}