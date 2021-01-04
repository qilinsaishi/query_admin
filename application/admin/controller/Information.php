<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\Information as InformationModel;

use app\common\validate\querylist\InformationValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class Information extends Admin
{
    protected $type = [
        1=>'综合',
        2=>'公告',
        3=>'赛事',
        4=>'攻略',
        5=>'社区'
    ];
    public function index()
    {
        $request = Request::param();
        $query = [
            'source' =>  isset($request['source']) ? $request['source'] : '',
            'type'     => isset($request['type']) ? $request['type'] : '',
            'q'       => isset($request['q']) ? $request['q'] : '',
            'game'  => isset($request['game']) ? $request['game'] : '',
        ];
        $args = [
            'query'  => $query,
            'field'  => '',
            'order'  => 'id desc',
            'params' => $query,
            'limit'  => 20,
        ];
		$gameList=config('app.game_type');
        // 分页列表
        $informationModel=new InformationModel();
        $list = $informationModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        $this->assign('typeList', $this->type);
		$this->assign('gameList', $gameList);
        return $this->fetch('index');
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
            $validate = new InformationValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['update_time']=date("Y-m-d H:i:s");
            $informationInfoObj= new InformationModel();
            $informationInfoObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($informationInfoObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $id = Request::param('id');
        $info = InformationModel::get($id);
        $gameList=config('app.game_type');

        $data = [
            'info'  => $info,
            'typeList'=>$this->type,
            'gameList'=>$gameList
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = InformationModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new InformationModel();
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
            $validate = new InformationValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['create_time']=date("Y-m-d H:i:s");
            $request['update_time']=date("Y-m-d H:i:s");

            $informationInfoObj = new InformationModel();
            $informationInfoObj->allowField(true)->save($request);

            if (is_numeric($informationInfoObj->id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        //$role_list=$this->role_list;

        return $this->fetch('create');
    }



}