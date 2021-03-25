<?php

namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\TeamInfo as TeamInfoModel;
use app\common\validate\querylist\CpseoTeamInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class TeamInfo extends Admin
{
    public function index()
    {
        $request = Request::param();
        $query = [
            'q' => isset($request['q']) ? $request['q'] : '',
            'game' => isset($request['game']) ? $request['game'] : '',
            'original_source' => isset($request['original_source']) ? $request['original_source'] : '',
        ];
        $args = [
            'query' => $query,
            'field' => '',
            'order' => 'team_id desc',
            'params' => $query,
            'limit' => 20,
        ];
        $gameList = config('app.game_type');
        $originalSource = config('app.original_source');
        // 分页列表
        $teameamModel = new TeamInfoModel();
        $list = $teameamModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        $this->assign('gameList', $gameList);
        $this->assign('originalSource', $originalSource);

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
            if (isset($request['aka']) && $request['aka']) {
                if (strpos($request['aka'], '，') !== false) {
                    $request['aka'] = str_replace('，', ',', $request['aka']);
                }
                $request['aka'] = explode(',', $request['aka']);
                $request['aka'] = json_encode($request['aka']);
            }
            //需要加事务
            Db::startTrans();
            try {
                $teamInfoObj = new teamInfoModel();
                $changeLog = ChangeLogs::checkInsertData('app\common\model\querylist\teamInfo', $request, $request['team_id'], 'team', $this->username, 'team_id');
                if ($changeLog) {
                    $rt = $teamInfoObj->isUpdate(true)->allowField(true)->save($request);
                    if (is_numeric($teamInfoObj->team_id)) {
                        // 提交事务
                        Db::commit();
                        return $this->response(200, Lang::get('Success'));
                    } else {
                        // 回滚事务
                        Db::rollback();
                        return $this->response(201, Lang::get('Fail'));
                    }
                } else {
                    // 回滚事务
                    Db::rollback();
                }

            } catch (\Exception $e) {
                dump($e->getMessage());
                // 回滚事务
                Db::rollback();
            }


        }

        $id = Request::param('id');
        $info = teamInfoModel::get($id);
        $typeList = config('app.game_type');
        $info['aka'] = json_decode($info['aka'], true);
        $info['race_stat'] = json_decode($info['race_stat'], true);
        if (!empty($info['aka'])) {
            $info['aka'] = implode(',', $info['aka']);
        } else {
            $info['aka'] = '';
        }
        /*if(!empty($info['race_stat'])){
            $info['race_stat']=implode(',',$info['race_stat']);
        }else{
            $info['race_stat']='';
        }*/

        $data = [
            'info' => $info,
            'typeList' => $typeList
        ];

        return $this->fetch('edit', $data);
    }

    public function remove()
    {
        $id = Request::param('id');

        $return = teamInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new teamInfoModel;
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
            $request['site_id'] = $this->site_id;
            if (isset($request['aka']) && $request['aka']) {
                if (strpos($request['aka'], '，') !== false) {
                    $request['aka'] = str_replace('，', ',', $request['aka']);
                }
                $request['aka'] = explode(',', $request['aka']);
                $request['aka'] = json_encode($request['aka']);
            }

            $teamInfoObj = new teamInfoModel();
            $teamInfoObj->allowField(true)->save($request);

            if (is_numeric($teamInfoObj->team_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $typeList = config('app.game_type');
        //$typeList=config('app.config_type');

        return $this->fetch('create', ['typeList' => $typeList]);
    }


}