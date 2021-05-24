<?php

namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\ScoreggTournamentInfo as ScoreggTournamentInfoModel;
use app\common\model\querylist\TeamList;
use app\common\validate\querylist\CpseoTeamInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class TournamentInfo extends Admin
{
    public function index()
    {
        $request = Request::param();
        $query = [
            'q' => isset($request['q']) ? $request['q'] : '',
            'game' => isset($request['game']) ? $request['game'] : 'lol',
        ];
        $args = [
            'query' => $query,
            'field' => '',
            'order' => 'tournament_id desc',
            'params' => $query,
            'limit' => 20,
        ];
        $gameList = config('app.game_type');
        // 分页列表
        $scoreggTournamentInfoModel = new ScoreggTournamentInfoModel();
        $list = $scoreggTournamentInfoModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        $this->assign('gameList', $gameList);

        return $this->fetch('index');
    }


    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            // 验证数据
           /** $validate = new CpseoTeamInfoValidate();
            $validateResult = $validate->scene('edit')->check($request);
            if (!$validateResult) {
                return $this->response(201, $validate->getError());
            }*/
           $request['start_time']=strtotime($request['start_time']);
		   $request['end_time']=strtotime($request['end_time']);

			$scoreggTournamentInfoObj = new ScoreggTournamentInfoModel();
			$rt = $scoreggTournamentInfoObj->isUpdate(true)->allowField(true)->save($request);
			if (is_numeric($scoreggTournamentInfoObj->tournament_id)) {
				return $this->response(200, Lang::get('Success'));
			} else {
				return $this->response(201, Lang::get('Fail'));
			}

        }

        $id = Request::param('id');
        $info = ScoreggTournamentInfoModel::get($id);
        $typeList = config('app.game_type');

        $data = [
            'info' => $info,
            'typeList' => $typeList
        ];

        return $this->fetch('edit', $data);
    }

    public function delete()
    {
        $id = Request::param('id');

        $return = ScoreggTournamentInfoModel::destroy($id);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }
    }

    public function handle()
    {
        $request = Request::instance()->param();

        $obj = new ScoreggTournamentInfoModel;
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




}