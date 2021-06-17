<?php
namespace app\admin\controller;

use think\facade\Request;
use think\facade\Lang;
use think\facade\Hook;
use app\common\model\ActiveList as ActiveListModel;
use app\common\model\ImageCategory as ImageCategoryModel;
use app\common\model\SiteConfig;
use app\admin\controller\Admin;
use app\common\validate\ActiveListValidate;

class ActiveList extends Admin
{
    

    public function index()
    {
        $request = Request::param();
        $siteModel=new \app\common\model\Site();
        // 获取分类列表
        $siteList=[];
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }

        $obj = new ActiveListModel;

        // 查询条件
        $map    = [];
        $params = [];
        $search = [];

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

        // 分页列表
        $list = $obj
            ->where($map)
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        if (!empty($list)) {
            foreach ($list as &$v) {

                $siteObj=$siteModel->getSiteNameById($v['site_id']);
                $v->site_name=$siteObj['name'] ?? '';
               
            }
        }



        $data = [
            'search'   => $search,
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
            $validate = new ActiveListValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            if ($request['start_time'] >$request['end_time']) {
                return $this->response(201, Lang::get('活动开始时间不能大于活动结束时间'));
            }
            $request['start_time']=strtotime($request['start_time']);
            $request['end_time']=strtotime($request['end_time']);
            $request['create_at']=time();
            $request['update_time']=time();


            $activeListObj = new ActiveListModel;
            $exist = $activeListObj->where('title', $request['title'])->value('id');
            if (is_numeric($exist)) {
                return $this->response(201, Lang::get('This record already exists'));
            }


           // $contentData = array_merge($request, $contentData);
            $activeListObj->allowField(true)->save($request);

            if (is_numeric($activeListObj->id)) {
               // $postData=['key_name'=>$request['flag'],'dataType' => 'imageList'];
               // $api_host=config('app.api_host').'/refresh';
               // $return=curl_post($api_host, $postData);
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
            'start_time'=>date("Y-m-d H:i:s"),
            'end_time'=>date("Y-m-d H:i:s",strtotime("+1 day")),
        ];

        return $this->fetch('create', $data);
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new ActiveListValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            if ($request['start_time'] >$request['end_time']) {
                return $this->response(201, Lang::get('活动开始时间不能大于活动结束时间'));
            }
            $request['update_time']=time();
            $request['start_time']=strtotime($request['start_time']);
            $request['end_time']=strtotime($request['end_time']);
            // 写入
            $activeListObj = new ActiveListModel;
            $activeListObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($activeListObj->id)) {
                //$postData=['key_name'=>$request['flag'],'dataType' => 'imageList'];
               // $api_host=config('app.api_host').'/refresh';
                //$return=curl_post($api_host, $postData);

                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $request = Request::param();

        // 获取分类列表
        $category = ImageCategoryModel::getCategoryList($this->site_id,'lol');

        $obj = new ActiveListModel;
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

        $obj = new ActiveListModel;
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

        // 删除
        $return = ActiveListModel::destroy($id);
        if ($return !== false) {
            //$postData=['key_name'=>$flag,'dataType' => 'imageList'];
           // $api_host=config('app.api_host').'/refresh';
          //  $return=curl_post($api_host, $postData);
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
        $obj = new ActiveListModel;
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