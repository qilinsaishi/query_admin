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
            $q           = ['name|key','like','%'.trim($request['q']).'%'];
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
        //查询所有可以站点下面的通用方法
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        $siteIds=array_column($siteList->toArray(),'id');

        $defaultObj = new DefaultConfigModel();
        $list = $defaultObj
            ->field('*')
            ->where($map)
            ->whereIn('site_id',$siteIds)
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
    //官网配置中心
    public function center()
    {
        $request = Request::param();
        $request['site_id']=2;

        $params = [];
        $search = [];
        $map    = [];

        if (isset($request['q'])) {
            $q           = ['name|key','like','%'.trim($request['q']).'%'];
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

        return $this->fetch('center');
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            $request['site_id']=$request['site_id'] ??2;
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
                $postData=['key_name'=>$request['key'],'dataType' => 'defaultConfig'];
                $api_host=config('app.api_host').'/refresh';
                $return=curl_post($api_host, $postData);

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
            'typeList'=>$typeList,
            'redirect_url'=>$_SERVER['HTTP_REFERER'],

        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');
        $key = Request::param('key');
        $return = DefaultConfigModel::destroy($id);

        if ($return !== false) {
            $postData=['key_name'=>$key,'dataType' => 'defaultConfig'];
            $api_host=config('app.api_host').'/refresh';
            $return=curl_post($api_host, $postData);
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
                $postData=['key_name'=>$request['key'],'dataType' => 'defaultConfig'];
                $api_host=config('app.api_host').'/lol/refresh';
                $return=curl_post($api_host, $postData);
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

        return $this->fetch('create',['typeList'=>$typeList,'siteList'=>$siteList,'redirect_url'=>$_SERVER['HTTP_REFERER']]);
    }


}