<?php
namespace app\admin\controller;

use app\common\model\querylist\KeywordsList as KeywordsListModel;
use app\common\model\querylist\ScwsKeywordMap;
use app\common\validate\querylist\KeywordsListValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use app\common\model\Auth;
use app\admin\controller\Admin;


class KeywordsList extends Admin
{
    public function index()
    {
        $request = Request::param();

        $params = [];
        $search = [];

        if (isset($request['q'])) {
            $q           = trim($request['q']);
            $params['q'] = $request['q'];
            $search['q'] = $request['q'];
        }else {
            $q = '';
            $search['q'] = '';
        }

        $keywordsListObj = new KeywordsListModel();
        $list = $keywordsListObj
            ->field('*')
            ->whereOr('word','like','%'.$q.'%')
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
	//关键词映射
	public function scws_keyword()
    {
        $request = Request::param();

        $params = [];
        $search = [];

        if (isset($request['q'])) {
            $q           = trim($request['q']);
            $params['q'] = $request['q'];
            $search['q'] = $request['q'];
        }else {
            $q = '';
            $search['q'] = '';
        }

        $keywordsMapObj = new ScwsKeywordMap();
        $list = $keywordsMapObj
            ->field('*')
            ->whereOr('keyword','like','%'.$q.'%')
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

        return $this->fetch('scws_keyword_map');
    }
    public function checkStatus(){
        $request = Request::param();
        $request['update_time']=date("Y-m-d H:i:s");
        $scwsKeywordMapObj= new ScwsKeywordMap();
        $scwsKeywordMapObj->isUpdate(true)->allowField(true)->save($request);

        if (is_numeric( $scwsKeywordMapObj->id)) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }

    }
    public function checkHandle()
    {
        $request = Request::instance()->param();

        $obj = new ScwsKeywordMap;
        switch ($request['type']) {

            case 'enabled':
                foreach ($request['ids'] as $v) {
                    $result = $obj
                        ->where('id', $v)
                        ->setField('available', 1);
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
            case 'disabled':
                foreach ($request['ids'] as $v) {
                    $result = $obj
                        ->where('id', $v)
                        ->setField('available', 0);
                }

                if ($result !== false) {
                    return $this->response(200, Lang::get('Success'));
                }
                break;
        }

        return $this->response(201, Lang::get('Fail'));
    }


    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new KeywordsListValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['update_time']=date("Y-m-d H:i:s");
             $keywordsListObj= new KeywordsListModel();
             $keywordsListObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric( $keywordsListObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = KeywordsListModel::get($id);
        $gameList=config('app.game_type');

        $data = [
            'info'  => $info,
            'gameList'=>$gameList

        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = KeywordsListModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new KeywordsListModel;
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
            $validate = new KeywordsListValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['site_id']=$this->site_id;
            $request['create_time']=date("Y-m-d H:i:s");
            $request['update_time']=date("Y-m-d H:i:s");
            $keywordsListObj = new KeywordsListModel();
             $keywordsListObj->allowField(true)->save($request);

            if (is_numeric( $keywordsListObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $gameList=config('app.game_type');

        return $this->fetch('create',['gameList'=>$gameList]);
    }


}