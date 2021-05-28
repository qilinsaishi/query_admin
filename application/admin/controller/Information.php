<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
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
        $list=$list ?? [];

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

            //需要加事务
            Db::startTrans();
            try {
                $informationInfoObj= new InformationModel();
                $changeLog = ChangeLogs::checkInsertData('app\common\model\querylist\Information', $request, $request['id'], 'information', $this->username, 'id');
                if ($changeLog) {
                    $request['site']=$request['site'] ?? 0;
                    $old_request_game=$request['game'];//echo "oldgame:".$old_request_game."\n";
                    $request['update_time']=date("Y-m-d H:i:s",time());
                    if(isset($request['redirect']) && $request['redirect']>0){
                        $redirectInfo = $informationInfoObj->get($request['redirect']);//跳转的数据详情
                        if($request['game']!=$redirectInfo['game']){

                            $request['game']=$redirectInfo['game'];
                        }
                        if($request['source']!=$redirectInfo['source']){
                            $request['source']=$redirectInfo['source'];
                        }
                        if($request['site']!=$redirectInfo['site']){
                            $request['site']=$redirectInfo['site'];
                        }

                    }

                    $informationInfoObj->isUpdate(true)->allowField(true)->save($request);

                    if (is_numeric($informationInfoObj->id)) {
                        $postData=['params'=>json_encode([$informationInfoObj->id]),'dataType' => 'information'];
                        $api_host=config('app.api_host').'/refresh';
                        $return=curl_post($api_host, $postData);
                        //判断如果更新后和提交过来的数据不一致,则更新列表缓存
                        if($old_request_game !=$informationInfoObj->game){
                           // echo "oldgame:".$old_request_game."\n";
                            //原有的游戏资讯清除缓存
                            $oldPostData=['params'=>json_encode(["game"=>[$old_request_game]]),'dataType' => 'informationList'];
                            $oldReturn= $this->updateInformationCache($oldPostData);

                          //  echo "game:".$informationInfoObj->game."\n";
                            //现有的游戏的游戏资讯清除缓存
                            $postData=['params'=>json_encode(["game"=>[$informationInfoObj->game]]),'dataType' => 'informationList'];
                            $return=$this->updateInformationCache($postData);

                        }

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
        $info = InformationModel::get($id);
        $gameList=config('app.game_type');
        $siteList=[];
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }

        $data = [
            'info'  => $info,
            'typeList'=>$this->type,
            'gameList'=>$gameList,
            'siteList'=>$siteList
        ];

        return $this->fetch('edit', $data);
    }
    public function updateInformationCache($postData){
        $api_host=config('app.api_host').'/refresh';
        $return=curl_post($api_host, $postData);
        return $return;
    }
    public function view()
    {

        $id = Request::param('id');
        $info = InformationModel::get($id);
        $info['keywords_list']=json_decode($info['keywords_list'],true);
        $info['scws_list']=json_decode($info['scws_list'],true);
        $info['5118_word_list']=json_decode($info['5118_word_list'],true);//
        $info['baidu_word_list']=json_decode($info['baidu_word_list'],true);
        $keywords_list=$scws_list=$word_1185_list=[];
        if(isset($info['keywords_list'])){
            foreach ($info['keywords_list'] as $key=>$val){
                if(!empty($val)){
                    foreach ($val as $k=>&$v){
                        $v['keywords']=$k;
                    }
                    $keywords_list[$key]=array_values($val);

                }
            }
        }
        $scws_list=$info['scws_list'] ?? [];
        $word_1185_list=$info['5118_word_list'] ?? [];
        $baidu_word_list=$info['baidu_word_list'] ?? [];

        $data = [
            'info'  => $info,
            'keywords_list'=>$keywords_list,
            'scws_list'=>$scws_list,
            'word_1185_list'=>$word_1185_list,
            'baidu_word_list'=>$baidu_word_list,
        ];

        return $this->fetch('view', $data);
    }

    public function remove()
    {
        $id = Request::param('id');
        $informationInfo=InformationModel::get($id);
        $siteModel=new \app\common\model\Site();

        $return = InformationModel::destroy($id);
        if ($return !== false) {
            //刷新资讯列表第一页
            $postData=['params'=>json_encode([$id]),'dataType' => 'information'];
            $api_host=config('app.api_host').'/refresh';
            $return=curl_post($api_host, $postData);

            return $this->response(200, Lang::get('Success'),$return);
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
            $request['time_to_publish']=date("Y-m-d H:i:s",strtotime($request['time_to_publish']));
            $request['site']=$request['site'] ?? 0;
            $request['5118_rewrite']=0;
            $request['create_time']=date("Y-m-d H:i:s",time());
            $request['update_time']=date("Y-m-d H:i:s",time());
            $request['baidu_word_list']=json_encode([]);
            $informationInfoObj = new InformationModel();

            $informationInfoObj->allowField(true)->save($request);

            if (is_numeric($informationInfoObj->id)) {

                $postData=['params'=>json_encode(["game"=>[$request['game']]]),'dataType' => 'informationList'];
                $api_host=config('app.api_host').'/refresh';
                $return=curl_post($api_host, $postData);
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $siteList=[];
        $siteModel=new \app\common\model\Site();
        $siteList=$siteModel->getSiteList();
        if($siteList){
            $siteList=$siteList->toArray();
        }
        $curTime=date("Y-m-d H:i:s");
        $gameList=config('app.game_type');
        $data = [
            'typeList'=>$this->type,
            'gameList'=>$gameList,
            'siteList'=>$siteList,
            'curTime'=>$curTime
        ];

        return $this->fetch('create',$data);
    }



}