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
        $seoTeamModel=new CpseoTeamInfoModel();
        $seoTeamData=$seoTeamModel->getList($params,$q);
        $new_data=[];
        if (isset($seoTeamData)) {
            foreach ($seoTeamData as $v) {

                array_push($new_data, $v);
            }
        }
        $this->assign('search', $search);
        $this->assign('list', $new_data);
        $this->assign('page', $seoTeamData->render());

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
        $typeList=config('app.config_type');

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

            $cpseoTeamInfoObj = new CpseoTeamInfoModel();
            $cpseoTeamInfoObj->allowField(true)->save($request);

            if (is_numeric($cpseoTeamInfoObj->team_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $typeList=config('app.config_type');

        return $this->fetch('create',['typeList'=>$typeList]);
    }



}