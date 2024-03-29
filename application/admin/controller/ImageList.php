<?php
namespace app\admin\controller;

use think\facade\Request;
use think\facade\Lang;
use think\facade\Hook;
use app\common\model\ImageList as ImageListModel;
use app\common\model\ImageCategory as ImageCategoryModel;
use app\common\model\SiteConfig;
use app\admin\controller\Admin;
use app\common\validate\ImageCategoryValidate;
use app\common\validate\ImageListValidate;

class ImageList extends Admin
{
    // 分类配置标识
    protected $category = 'slider_category';

    public function index()
    {
        $request = Request::param();

        $obj = new ImageListModel;

        // 查询条件
        $map    = [];
        $params = [];
        $search = [];

        // 类别
        if (isset($request['cid']) && is_numeric($request['cid'])) {
            $cid           = ['cid','=',$request['cid']];
            $params['cid'] = $request['cid'];
            $search['cid'] = $request['cid'];
            array_push($map, $cid);
        }else {
            $cid = [];
            $search['cid'] = '';
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
        if (isset($request['q']) && $request['q']) {
            $q           = ['name','like','%'.$request['q'].'%'];
            $params['q'] = $request['q'];
            $search['q'] = $request['q'];
            array_push($map, $q );
        }else {
            $q = '';
            $search['q'] = '';
        }
        //查询所有可以站点下面的通用方法
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        $siteIds=array_column($siteList->toArray(),'id');

        // 分页列表
        $list = $obj
            ->where($map)
            ->whereIn('site_id', $siteIds)
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        $siteModel=new \app\common\model\Site();
        if (!empty($list)) {
            foreach ($list as &$v) {

                $siteObj=$siteModel->getSiteNameById($v['site_id']);
                $v->site_name=$siteObj['name'] ?? '';
                $v->catename = ImageCategoryModel::getNameById($this->site_id,'lol',$v->cid);
            }
        }

        // 获取分类列表
        $category = ImageCategoryModel::getCategoryList($this->site_id,'lol');
        $siteList=[];
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }

        $data = [
            'search'   => $search,
            'category' => $category,
            'list'     => $list,
            'siteList'     => $siteList,
            'page'     => $list->render(),
        
        ];

        return $this->fetch('index', $data);
    }

    public function create()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new ImageListValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $obj = new ImageListModel;
            $exist = $obj->where('name', $request['name'])->value('id');
            if (is_numeric($exist)) {
                return $this->response(201, Lang::get('This record already exists'));
            }

            // 写入content内容
            $contentData = [
                'uid'     => $this->uid,
            ];
            $contentData = array_merge($request, $contentData);
            $obj->allowField(true)->save($contentData);

            if (is_numeric($obj->id)) {
                $postData=['key_name'=>$request['flag'],'dataType' => 'imageList'];
                $api_host=config('app.api_host').'/refresh';
                $return=curl_post($api_host, $postData);

                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        // 获取分类列表

        $category = ImageCategoryModel::getCategoryList($this->site_id,'lol');
        $gameList=config('app.game_type');
        $siteList=[];
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }

        $data = [
            'category' => $category,
            'gameList' => $gameList,
            'siteList' => $siteList,
        ];

        return $this->fetch('create', $data);
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new ImageListValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            // 写入
            $obj = new ImageListModel;
            $obj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($obj->id)) {
                $postData=['key_name'=>$request['flag'],'dataType' => 'imageList'];
                $api_host=config('app.api_host').'/refresh';
                $return=curl_post($api_host, $postData);

                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $request = Request::param();

        // 获取分类列表
        $category = ImageCategoryModel::getCategoryList($this->site_id,'lol');

        $obj = new ImageListModel;
        $info = $obj->where('id', $request['id'])->find();
        $gameList=config('app.game_type');
        $siteList=[];
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }

        $data = [
            'category' => $category,
            'gameList' => $gameList,
            'siteList' => $siteList,
            'info'  => $info,
        ];

        return $this->fetch('edit', $data);
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new ImageListModel;
        switch ($request['type']) {
            case 'delete':
                foreach ($request['ids'] as $v) {
                    $result = $obj::destroy($v);
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
            case 'active':
                foreach ($request['ids'] as $v) {
                    $result = $obj
                        ->where('id', $v)
                        ->setField('status', 1);
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
            case 'freeze':
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

    public function remove()
    {
        $id = Request::param('id');
        $flag = Request::param('flag');
        // 删除
        $return = ImageListModel::destroy($id);
        if ($return !== false) {
            $postData=['key_name'=>$flag,'dataType' => 'imageList'];
            $api_host=config('app.api_host').'/refresh';
            $return=curl_post($api_host, $postData);
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }

    }

    public function removeCategory()
    {
        $id = Request::param('id');
        $del = ImageCategoryModel::destroy($id);

        if ($del !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handleCategory()
    {
        $data = Request::instance()->param();
        $imageCategoryModel=new ImageCategoryModel();
        $obj = new ImageListModel;
        switch ($data['type']) {
            case 'save':
                if (!empty($data['id'])) {
                    foreach ($data['id'] as $k => $v) {
                        $updateData = [
                            'id'       => $data['id'][$k],
                            'name'     => $data['name'][$k],
                            'game'     => $data['game'][$k],
                            'sort' => $data['sort'][$k],
                        ];

                        $result = $imageCategoryModel->isUpdate(true)->allowField(true)->save($updateData);;
                    }
                }

                if (!empty($data['temp_name'])) {
                    foreach ($data['temp_name'] as $k => $v) {
                        $insertData = [
                            'name'     => $v,
                            'site_id'     => $this->site_id,
                            'game' => $data['temp_game'][$k],
                            'sort' => $data['temp_sort'][$k],
                        ];
                        $result =$imageCategoryModel->allowField(true)->insert($insertData);
                        //$result = SiteConfig::insertCategoryConfig($this->site_id, $this->category, $insertData);
                    }
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
        }

        return $this->response(201, Lang::get('Fail'));
    }

    public function category()
    {
        // 获取分类列表
        $category = ImageCategoryModel::getCategoryList($this->site_id,'lol');
        $gameList=config('game_type');
        $data = [
            'category' => $category,
            'gameList'=>$gameList
        ];

        return $this->fetch('category', $data);
    }
}