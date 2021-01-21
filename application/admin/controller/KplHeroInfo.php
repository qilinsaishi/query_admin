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
            if(isset($request['aka']) && $request['aka']){

                if(strpos($request['aka'],'，') !==false){
                    $request['aka']=str_replace('，',',',$request['aka']);
                }
                $request['aka']=explode(',',$request['aka']);
                $request['aka']=json_encode($request['aka']);
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
        $akaArray = $statArray=$skinlist=$skillList=[];

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
        $skillList=[];
        if ($info && $info['inscription_tips']) {
            $skillList = json_decode($info['inscription_tips'], true);
        }
        //$this->assign('inscription_tips', $skillList);

        $kplInscriptionModel=new \app\common\model\querylist\KplInscriptionInfo();
        $kplInscriptionList=$kplInscriptionModel->getSelect();
//print_r($skillList['sugglistIds']);exit;
        $hero_type=$this->hero_type;
        $data = [
            'info' => $info,
            'heroType' => $hero_type,
            'akaArray' => $akaArray,
            'statArray' => $statArray,
            'sugglistIds' => $skillList['sugglistIds'],
            'suggTips'=>$skillList['suggTips'],
            'kplInscriptionList'=>$kplInscriptionList->toArray(),
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
            if(isset($request['aka']) && $request['aka']){

                if(strpos($request['aka'],'，') !==false){
                    $request['aka']=str_replace('，',',',$request['aka']);
                }
                $request['aka']=explode(',',$request['aka']);
                $request['aka']=json_encode($request['aka']);
            }
            $request['skill_list']=json_encode([]);

            $heroInfoObj = new KplHeroInfoModel();
            $heroInfoObj->allowField(true)->save($request);

            if (is_numeric($heroInfoObj->hero_id)) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $hero_type=$this->hero_type;

        return $this->fetch('create', ['heroType' => $hero_type]);
    }
    //皮肤列表
    public function skinList(){
        $id = Request::param('id');
        $info = KplHeroInfoModel::get($id);
        $skinlist=[];
        if ($info && $info['skin_list']) {
            $skinlist = json_decode($info['skin_list'], true);
        }
        $this->assign('skinlist', $skinlist);
        $this->assign('id', $id);
        return $this->fetch('skin_list');

    }
    //皮肤编辑
    public function skinEdit(){
        // 处理AJAX提交数据
        $heroInfoObj = new KplHeroInfoModel();
        if (Request::isAjax()) {
            $data= Request::param();
            $id = $data['id'] ?? '';
            $key= $data['key'] ?? '';
            unset($data['id']);
            unset($data['key']);
            //echo 'id:'.$id.'key:'.$key;
            $info = $heroInfoObj->get($id);
            $skinlist = json_decode($info['skin_list'], true);
            $skinlist[$key]=$data;
            $updateData=json_encode($skinlist);
            $cdata=[];
            if(!empty($updateData)){
                $cdata=[
                    'hero_id'=>$id,
                    'update_time'=>date('Y-m-d H:i:s'),
                    'skin_list'=>$updateData
                ];
            }
            $result = $heroInfoObj->isUpdate(true)->allowField(true)->save($cdata);
            if ($result !== false) {
                return $this->response(200, Lang::get('Success'));
            }else{
                return $this->response(201, Lang::get('Fail'));
            }

        }

        $id = Request::param('id');
        $key = Request::param('key');
        $info = KplHeroInfoModel::get($id);
        $skinlist = json_decode($info['skin_list'], true);
        $info=$skinlist[$key];

        $data = [
            'info' => $info,
            'id' => $id,
            'keys' => $key,
        ];

        return $this->fetch('skin_edit', $data);
    }
    //新增皮肤
    public function  skinCreate(){

        $heroInfoObj = new KplHeroInfoModel();
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $data= Request::param();
            $id = $data['id'] ?? '';
            unset($data['id']);
            $tempArr=[
                'name'=>$data['name'],
                'bigImg'=>$data['bigImg'],
            ];

            //echo 'id:'.$id.'key:'.$key;
            $info = $heroInfoObj->get($id);
            $skinlist = json_decode($info['skin_list'], true);
            $count=count($skinlist) ?? 0;
            if($count){
                $skinlist[$count]=$tempArr;
            }
            $updateData=json_encode($skinlist);
            $cdata=[];
            if(!empty($updateData)){
                $cdata=[
                    'hero_id'=>$id,
                    'create_time'=> date("Y-m-d H:i:s"),
                    'update_time'=> date("Y-m-d H:i:s"),
                    'skin_list'=>$updateData
                ];
            }
            $heroInfoObj = new KplHeroInfoModel();
            $result = $heroInfoObj->isUpdate(true)->allowField(true)->save($cdata);

            if ($result) {
                return $this->response(200, Lang::get('Success'),['id'=>$id]);
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $id = Request::param('id');

        return $this->fetch('skin_create', ['id' => $id]);
    }
    //删除皮肤
    public function skinRemove(){
        $id = Request::param('id');
        $key= Request::param('key');
        $heroInfoObj = new KplHeroInfoModel();
        $info = $heroInfoObj->get($id);
        $skinlist = json_decode($info['skin_list'], true);
        if($skinlist[$key]){
            unset($skinlist[$key]);
        }
        $updateData=json_encode($skinlist);
        $cdata=[
            'hero_id'=>$id,
            'update_time'=>date('Y-m-d H:i:s'),
            'skin_list'=>$updateData
        ];
        $return='';
        $return=$heroInfoObj->isUpdate(true)->allowField(true)->save($cdata);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'));
        } else {
            return $this->response(201, Lang::get('Fail'));
        }

    }

    //技能列表
    public function skillList(){
        $id = Request::param('id');
        $info = KplHeroInfoModel::get($id);
        $skillList=[];
        if ($info && $info['skill_list']) {
            $skillList = json_decode($info['skill_list'], true);
        }
        $this->assign('skill_list', $skillList);
        $this->assign('id', $id);
        return $this->fetch('skill_list');

    }
    //新增技能
    public function  skillCreate(){

        $heroInfoObj = new KplHeroInfoModel();
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $data= Request::param();
            $id = $data['id'] ?? '';
            unset($data['id']);
            $tempArr=[
                'name'=>$data['name'],
                'killImg'=>$data['killImg'],
                'cooling'=>$data['cooling'],
                'consume'=>$data['consume'],
                'skillDesc'=>$data['skillDesc'],
            ];

            //echo 'id:'.$id.'key:'.$key;
            $info = $heroInfoObj->get($id);
            $skilllist = json_decode($info['skill_list'], true);
            $count=count($skilllist) ?? 0;
            if($count >=0){
                $skilllist[$count]=$tempArr;
            }
            $updateData=json_encode($skilllist);
            $cdata=[];
            if(!empty($updateData)){
                $cdata=[
                    'hero_id'=>$id,
                    'create_time'=> date("Y-m-d H:i:s"),
                    'update_time'=> date("Y-m-d H:i:s"),
                    'skill_list'=>$updateData
                ];
            }
            $heroInfoObj = new KplHeroInfoModel();
            $result = $heroInfoObj->isUpdate(true)->allowField(true)->save($cdata);

            if ($result) {
                return $this->response(200, Lang::get('Success'),['id'=>$id]);
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }
        $id = Request::param('id');

        return $this->fetch('skill_create', ['id' => $id]);
    }
    //删除技能
    public function skillRemove(){
        $id = Request::param('id');
        $key= Request::param('key');
        $heroInfoObj = new KplHeroInfoModel();
        $info = $heroInfoObj->get($id);
        $skilllist = json_decode($info['skill_list'], true);
        if($skilllist[$key]){
            unset($skilllist[$key]);
        }
        $updateData=json_encode($skilllist);
        $cdata=[
            'hero_id'=>$id,
            'update_time'=>date('Y-m-d H:i:s'),
            'skill_list'=>$updateData
        ];
        $return='';
        $return=$heroInfoObj->isUpdate(true)->allowField(true)->save($cdata);

        if ($return !== false) {
            return $this->response(200, Lang::get('Success'),['key'=>$key]);
        } else {
            return $this->response(201, Lang::get('Fail'));
        }

    }

    //编辑技能
    public function skillEdit(){
        // 处理AJAX提交数据
        $heroInfoObj = new KplHeroInfoModel();
        if (Request::isAjax()) {
            $data= Request::param();
            $id = $data['id'] ?? '';
            $key= $data['key'] ?? '';
            unset($data['id']);
            unset($data['key']);
            //echo 'id:'.$id.'key:'.$key;
            $info = $heroInfoObj->get($id);
            $skilllist = json_decode($info['skill_list'], true);
            $skilllist[$key]=$data;
            $updateData=json_encode($skilllist);
            $cdata=[];
            if(!empty($updateData)){
                $cdata=[
                    'hero_id'=>$id,
                    'update_time'=>date('Y-m-d H:i:s'),
                    'skill_list'=>$updateData
                ];
            }
            $result = $heroInfoObj->isUpdate(true)->allowField(true)->save($cdata);
            if ($result !== false) {
                return $this->response(200, Lang::get('Success'));
            }else{
                return $this->response(201, Lang::get('Fail'));
            }

        }

        $id = Request::param('id');
        $key = Request::param('key');
        $info = KplHeroInfoModel::get($id);
        $skinlist = json_decode($info['skill_list'], true);
        $info=$skinlist[$key];

        $data = [
            'info' => $info,
            'id' => $id,
            'keys' => $key,
        ];

        return $this->fetch('skill_edit', $data);
    }


}