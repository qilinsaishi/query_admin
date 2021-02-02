<?php
namespace app\admin\controller;

use app\common\model\DefaultConfig as DefaultConfigModel;
use app\common\validate\DefaultConfigValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use app\common\model\Auth;
use app\admin\controller\Admin;


class DefaultConfig extends Admin
{
    public function index()
    {
        $request = Request::param();

        $params = [];
        $search = [];
        $map    = [];

        if (isset($request['q'])) {
            $q           = ['name','like','%'.$request['q'].'%'];
            $params['q'] = $request['q'];
            $search['q'] = $request['q'];
            array_push($map, $q );
        }else {
            $q = '';
            $search['q'] = '';
        }
        if (isset($request['site_id']) && is_numeric($request['site_id'])) {
            $site_id           = ['site_id','=',$request['site_id']];
            $params['site_id'] = $request['site_id'];
            $search['site_id'] = $request['site_id'];
            array_push($map, $site_id);
        }else {
            $site_id = [];
            $search['site_id'] = '';
        }

        $defaultObj = new DefaultConfigModel();
        $list = $defaultObj
            ->field('*')
            ->where($map)
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        $new_list = [];
        $siteModel=new \app\common\model\Site();
        if (isset($list)) {
            foreach ($list as $v) {
                $siteObj=$siteModel->getSiteNameById($v['site_id']);
                $v['site_name']=$siteObj['name'] ?? '';
                array_push($new_list, $v);
            }
        }

        $siteList=[];
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }
        $this->assign('search', $search);
        $this->assign('list', $new_list);
        $this->assign('siteList', $siteList);
        $this->assign('page', $list->render());

        return $this->fetch('index');
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            if($request['type']=='text'){
                $request['value']=$request['textvalue'];
            }elseif($request['type']=='images'){
                $request['value']=$request['logovalue'];
            }elseif($request['type']=='file'){
                $request['value']=$request['filevalue'];
            }
            unset($request['logovalue']);
            unset($request['filevalue']);
            unset($request['textvalue']);
            // 验证数据
            $validate = new DefaultConfigValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $defaultConfigObj= new DefaultConfigModel();
            $defaultConfigObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($defaultConfigObj->id)) {
                $api_host=config('app.api_host').'/lol/refresh?dataType=defaultConfig&key_name='.$request['key'];
                file_get_contents($api_host);
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = DefaultConfigModel::get($id);
        $typeList=config('app.config_type');
        $siteList=[];
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }

        $data = [
            'info'  => $info,
            'siteList'=>$siteList,
            'typeList'=>$typeList

        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = DefaultConfigModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new DefaultConfigModel;
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
            if($request['type']=='text'){
                $request['value']=$request['textvalue'];
            }elseif($request['type']=='images'){
                $request['value']=$request['logovalue'];
            }elseif($request['type']=='file'){
                $request['value']=$request['filevalue'];
            }
            unset($request['logovalue']);
            unset($request['filevalue']);
            unset($request['textvalue']);
            // 验证数据
            $validate = new DefaultConfigValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }


            //$request['site_id']=$this->site_id;
            $defaultConfigObj = new DefaultConfigModel();
            $defaultConfigObj->allowField(true)->save($request);

            if (is_numeric($defaultConfigObj->id)) {
                $api_host=config('app.api_host').'/lol/refresh?dataType=defaultConfig&key_name='.$request['key'];
                file_get_contents($api_host);
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $siteList=[];
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }

        $typeList=config('app.config_type');

        return $this->fetch('create',['typeList'=>$typeList,'siteList'=>$siteList]);
    }


}