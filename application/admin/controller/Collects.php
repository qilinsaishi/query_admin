<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\CpseoTeamInfo;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class Collects extends Admin
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

        $collectObj = new Collect();

        $seoTeamModel=new CpseoTeamInfo();
        $seoTeamData=$seoTeamModel->getList($params,$q);
        $new_data=[];
        if (isset($seoTeamData)) {
            foreach ($seoTeamData as $v) {

                array_push($new_data, $v);
            }
        }
        print_r($new_data);exit;

        $list = $collectObj
            ->field('*')
            ->whereOr('title|keyword|url','like','%'.$q.'%')
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        $new_list = [];

        if (isset($list)) {
            foreach ($list as $v) {

                array_push($new_list, $v);
            }
        }

        $this->assign('search', $search);
        $this->assign('list', $new_list);
        $this->assign('page', $list->render());

        return $this->fetch('index');
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new SiteValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $siteObj = new SiteModel;
            $siteObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($siteObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = SiteModel::get($id);

        $data = [
            'info'  => $info,
            'theme' => $this->getThemeList(),
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = SiteModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function change()
    {
        $request = Request::param();

        if (empty($request['id'])) {
            return $this->response(201, Lang::get('Parameter missing'));
        }

        // 验证是否有权限管理该站点 (预防AJAX提交不属于自己管理的站点ID)
        $auth = new Auth;
        $ids = $auth->getSiteIds($this->uid);
        if (!in_array($request['id'], $ids)) {
            return $this->response(201, Lang::get('Fail'));
        }

        // 判断站点是否存在并设置session
        $siteObj = new SiteModel;
        $site =$siteObj
            ->field('id,name,domain,theme')
            ->where('id', $request['id'])
            ->find();

        if(isset($site)) {
            Session::set('site_id', $site['id'], 'admin');
            Session::set('site_name', $site['name'], 'admin');
            Session::set('site_url', $site['domain'], 'admin');
            Session::set('site_theme', isset($site['theme']) ? $site['theme'] : 'default', 'admin');
        }

        if (Session::has('site_id', 'admin')) {
            return $this->response(200, Lang::get('Success'));
        } else {
             return $this->response(201, Lang::get('Fail'));
        }
    }

    public function create()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new SiteValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $siteObj = new SiteModel;
            $siteObj->allowField(true)->save($request);

            if (is_numeric($siteObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $data = [
            'theme'      => $this->getThemeList(),
        ];

        return $this->fetch('create', $data);
    }


}