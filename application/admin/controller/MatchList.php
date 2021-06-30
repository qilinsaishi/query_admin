<?php

namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\ScoreggMatchList as ScoreggMatchListModel;
use app\common\model\querylist\ScoreggTournamentInfo;
use app\common\model\querylist\ShangniuMatchList as ShangniuMatchListModel;
use app\common\model\querylist\ShangniuTournamentInfo;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class MatchList extends Admin
{
    public function index()
    {
        $request = Request::param();
        $source_from=$request['source_from'] ?? 'scoregg';
        $default_game=($source_from=='shangniu') ?'dota2':'lol';
        $query = [
            'q' => isset($request['q']) ? $request['q'] : '',
            'game' => isset($request['game']) ? $request['game'] :$default_game,
            'match_status' => isset($request['match_status']) ? $request['match_status'] :'-1',
        ];
        $args = [
            'query' => $query,
            'field' => '',
            'order' => 'tournament_id desc',
            'params' => $query,
            'limit' => 20,
        ];
        $gameList = config('app.game_type');
        $matchStatusList=[];
        // 分页列表
        if($source_from =='shangniu'){
            $matchStatusList=config('app.shangniuMatchStatus');
            $shangniuMatchListModel = new ShangniuMatchListModel();
            $matchList = $shangniuMatchListModel->getList($args);
        }else{
            $matchStatusList=config('app.scoreggMatchStatus');
            $scoreggMatchListModel = new ScoreggMatchListModel();
            $matchList = $scoreggMatchListModel->getList($args);
        }
        if( count($matchList)>0){
            foreach ($matchList as $matchInfo){
                if($matchInfo['game']=='dota2'){
                    $tournamentModel=new ShangniuTournamentInfo();
                }else{
                    $tournamentModel=new ScoreggTournamentInfo();
                }
                $tournamentmMap['tournament_id']=$matchInfo['tournament_id'];
                $tournamentInfo=$tournamentModel->getTournamentInfo($tournamentmMap,'tournament_name');
                $matchInfo['tournament_name']=$tournamentInfo['tournament_name'] ?? '';
                $teamModel=new \app\common\model\querylist\TeamInfo();
                //主队名称
                $homeTeamMap['site_id']=$matchInfo['home_id'];
                $homeTeamMap['original_source']=$source_from;
                $homeTeamMap['game']=$matchInfo['game'];
                $homeTeamInfo=$teamModel->getTeamInfo($homeTeamMap,'team_name,logo');
                //客队名称
                $homeTeamMap['site_id']=$matchInfo['away_id'];
                $homeTeamMap['original_source']=$source_from;
                $homeTeamMap['game']=$matchInfo['game'];
                $awayTeamInfo=$teamModel->getTeamInfo($homeTeamMap,'team_name,logo');
                $matchInfo['home_name']=$homeTeamInfo['team_name'] ?? '';
                $matchInfo['away_name']=$awayTeamInfo['team_name'] ?? '';
                $matchInfo['home_logo']=$homeTeamInfo['logo'] ?? '';
                $matchInfo['away_logo']=$awayTeamInfo['logo'] ?? '';

            }

        }

        $this->assign('search', $query);
        $this->assign('matchStatusList', $matchStatusList);
        $this->assign('source_from', $source_from);
        $this->assign('default_game', $default_game);
        $this->assign('list', $matchList);
        $this->assign('page', $matchList->render());
        $this->assign('gameList', $gameList);

        return $this->fetch('index');
    }


    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();

            $source_from=$request['source_from'] ?? 'scoregg';
            $postData=['game'=>$request['game'],'match_id' =>$request['match_id']];

            $api_host=config('app.api_host').'/refreshGame';
            $return=curl_post($api_host, $postData);

			if ($return) {

				return $this->response(200, Lang::get('Success'),$source_from );
			} else {
				return $this->response(201, Lang::get('Fail'));
			}

        }


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