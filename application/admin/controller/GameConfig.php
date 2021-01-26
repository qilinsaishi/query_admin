<?php
namespace app\admin\controller;

use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\GameConfig as GameConfigModel;
use app\common\model\Auth;
use app\admin\controller\Admin;
use app\common\validate\GameConfigValidate;

class GameConfig extends Admin
{
    
	public function index()
    {
        $request = Request::param();

        $gameConfigObj = new GameConfigModel;

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


        // 分页列表
        $list = $gameConfigObj
            ->whereOr('title','like','%'.$q.'%')
            //->where('id', 'in', $ids)
            ->order('id asc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        $data = [
            'search' => $search,
            'list'   => $list,
            'page'   => $list->render(),
        
        ];

        return $this->fetch('index', $data);
    }
    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            // 验证数据
            $validate = new GameConfigValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $gameConfigObj = new GameConfigModel;
            $gameConfigObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($gameConfigObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = GameConfigModel::get($id);
        $gameList=config('app.game_type');

        $data = [
            'info'  => $info,
            'typeList'=>$gameList,
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = GameConfigModel::destroy($id);

        if ($return !== false) {
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
            $validate = new GameConfigValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }

            $gameConfigObj = new GameConfigModel;
            $gameConfigObj->allowField(true)->save($request);

            if (is_numeric($gameConfigObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $gameList=config('app.game_type');
        $data = [
            'theme'      => $this->getThemeList(),
            'typeList'=>$gameList,
        ];

        return $this->fetch('create', $data);
    }

    
}