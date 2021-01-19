<?php

namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\KplHeroInfo as KplHeroInfoModel;
use app\common\validate\querylist\KplHeroInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class KplHeroInfo extends Admin
{
    //职业列表
    protected $hero_type = [
        1 => '战士',
        2 => '法师',
        3 => '坦克',
        4 => '刺客',
        5 => '射手',
        6 => '辅助',
        10 => '限免',
        11 => '新手'
    ];

    public function index()
    {
        $request = Request::param();
        $query = [
            'type'     => isset($request['type']) ? $request['type'] : '',
            'q'       => isset($request['q']) ? $request['q'] : '',
        ];
        $args = [
            'query'  => $query,
            'field'  => '',
            'order'  => 'id desc',
            'params' => $query,
            'limit'  => 20,
        ];
        $hero_type=$this->hero_type;
        // 分页列表
        $heroInfoModel = new KplHeroInfoModel();
        $list = $heroInfoModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());

        $this->assign('heroType', $hero_type);
        return $this->fetch('index');
    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据 json_decode($info['stat'], true);
            $validate = new KplHeroInfoValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['stat']=json_encode($request['statArray']);
            $request['update_time'] = date("Y-m-d H:i:s");
            $heroInfoObj = new KplHeroInfoModel();
            $heroInfoObj->isUpdate(true)->allowField(true)->save($request);

            if (is_numeric($heroInfoObj->hero_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }

        $id = Request::param('id');
        $info = KplHeroInfoModel::get($id);
        $akaArray = $statArray=$skinlist=[];

        if ($info && $info['aka']) {
            $akaArray = json_decode($info['aka'], true);
            if(!empty($akaArray)){
                $info['aka']=join(',',$akaArray);
            }
        }

        if ($info && $info['stat']) {
            $statArray = json_decode($info['stat'], true);
        }
        if ($info && $info['skin_list']) {
            $skinlist = json_decode($info['skin_list'], true);
        }
        if ($info && $info['skill_list']) {
            $skillList = json_decode($info['skill_list'], true);
        }

        $hero_type=$this->hero_type;
        $data = [
            'info' => $info,
            'heroType' => $hero_type,
            'akaArray' => $akaArray,
            'statArray' => $statArray,
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = KplHeroInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new KplHeroInfoModel();
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
            $validate = new KplHeroInfoValidate();
            $validateResult = $validate->scene('create')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }
            $request['create_time'] = date("Y-m-d H:i:s");
            $request['update_time'] = date("Y-m-d H:i:s");

            $heroInfoObj = new KplHeroInfoModel();
            $heroInfoObj->allowField(true)->save($request);

            if (is_numeric($heroInfoObj->hero_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $hero_type=$this->hero_type;

        return $this->fetch('create', ['hero_type' => $hero_type]);
    }


}