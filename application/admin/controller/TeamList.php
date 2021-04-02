<?php

namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\TeamInfo as TeamInfoModel;
use app\common\model\querylist\TeamList as TeamListModel;
use app\common\validate\querylist\CpseoTeamInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class TeamList extends Admin
{
    
    public function lists()
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
            'order' => 'tid desc',
            'params' => $query,
            'limit' => 20,
        ];
        $gameList = config('app.game_type');
        $originalSource = config('app.original_source');
        // 分页列表
        $teamInfoModel = new TeamListModel();
        $list = $teamInfoModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        $this->assign('gameList', $gameList);
        $this->assign('originalSource', $originalSource);

        return $this->fetch('lists');
    }
    public function updataList(){
        $request = Request::param();

        $tid=$request['tid'];
        unset($request['tid']);
        if($request['value']!='' || $request['value']>0){
            if($request['field']=='aka'){
                if(strpos($request['value'],',')!==false){
                    $aka=explode(',',$request['value']);
                }else{
                    $aka[]=$request['value'];
                }
                $request['value']=json_encode($aka);
            }
            $data[$request['field']]=$request['value'];
            $teamListObj = new TeamListModel();
            $return=$teamListObj->updateField($tid,$data);

            if ($return !== false) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }else{
            return $this->response(201, '数据值不能为空');
        }


    }
    public function selectHmtl(){
        $tid = Request::param('tid');
        $field = Request::param('field');
        $teamInfoModel = new TeamInfoModel();
        $map['tid']=$tid;
        $teamInfos=$teamInfoModel->getFieldList($map,$field);
        $html='<select class="filed_select" style="width:180px;height: 30px;line-height: 30px;margin: 0 auto;" name="'.$field.'"><option value="">请选择</option>';
        if(count($teamInfos)>0){
            foreach ($teamInfos as $key=>$val){
                if($field=='aka'){
                    $val=json_decode($val,true);
                    if(!empty($val)){
                        $val=join(',',$val);
                    }else{
                        $val='';
                    }
                }

                if($val !=''){
                    if($field !='original_source' || $field !='game'){
                        $html.='<option value="'.$key.'">'.$val.'</option>';
                    }else{
                        $html.='<option value="'.$val.'">'.$val.'</option>';
                    }

                }
            }
        }
        $html.='</select>';
        $html.='<input type="hidden" name="tid" value="'.$tid.'">';
        $html.='<input type="hidden" name="field" value="'.$field.'">';
        return $this->response(200, Lang::get('Success'),$html);

    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            $tid=$request['tid'];
            unset($request['tid']);
            $teamListObj = new TeamListModel();
            $return=$teamListObj->updateField($tid,$request);

            if ($return !== false) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }

        }

        $id = Request::param('id');
        $postData=json_encode(['tid'=>$id,'type' => 'team']);
        $api_host=config('app.api_host').'/getIntergration';
        $return=curl_post($api_host, $postData);
        $return=json_decode($return,true);
        $info = TeamListModel::with(['teamMap.info'])->get($id);

        $child=[];
        if(count($info['teamMap'])>0) {
            foreach ($info['teamMap'] as $k=>$v){
                $child[$k]['team_name']=$v['info']['team_name'] ?? '-';
                $child[$k]['team_id']=$v['info']['team_id'] ?? '-';
                $child[$k]['en_name']=$v['info']['en_name'] ?? '-';
                $child[$k]['cn_name']=$v['info']['cn_name'] ?? '-';
                $child[$k]['established_date']=$v['info']['established_date'] ?? '-';
                $child[$k]['coach']=$v['info']['coach'] ?? '-';
                $child[$k]['logo']=$v['info']['logo'] ?? '';
                $child[$k]['location']=$v['info']['location'] ?? '-';
                $child[$k]['site_id']=$v['info']['site_id'] ?? '-';
                $child[$k]['game']=$v['info']['game'] ?? '-';
                $child[$k]['description']=$v['info']['description'] ?? '-';
                $child[$k]['honor_list']=$v['info']['honor_list'] ?? '-';
                $child[$k]['team_history']=htmlspecialchars_decode(strip_tags($v['info']['team_history'])) ?? '-';
                $child[$k]['race_stat']=$v['info']['race_stat'] ?? '-';
                $caka=json_decode($v['info']['aka'],true);
                if(count($caka)>0){
                    $child[$k]['aka']=join(',',$caka);
                }else{
                    $child[$k]['aka']='-';
                }
                $child[$k]['original_source']=$v['info']['original_source'] ?? '';
                $child[$k]['create_time']=$v['info']['create_time'] ?? '';
                $child[$k]['update_time']=$v['info']['update_time'] ?? '';
                $child[$k]['tid']=$v['info']['tid'] ?? '';
                unset($v['info']);
            }
        }
        $info['team_name']=$return['structure']['team_name'] ?? '-';
        $info['game']=$return['structure']['game'] ?? '-';
        $info['cn_name']=$return['structure']['cn_name'] ?? '-';
        $info['en_name']=$return['structure']['en_name'] ?? '-';
        $info['established_date']=$return['structure']['established_date'] ?? '-';
        $info['coach']=$return['structure']['coach'] ?? '-';
        $info['coach']=$return['structure']['coach'] ?? '-';
        $info['logo']=$return['structure']['logo'] ?? '-';
        $info['description']=$return['structure']['description'] ?? '-';
        $info['honor_list']=$return['structure']['honor_list'] ?? '-';
        $info['team_history']=$return['structure']['team_history'] ?? '-';
        $info['race_stat']=$return['structure']['race_stat'] ?? '-';
        $info['location']=$return['structure']['location'] ?? '-';
        $info['hot']=$return['structure']['hot'] ?? '-';
        $info['site_id']=$return['structure']['site_id'] ?? '-';
        $info['original_source']=$return['structure']['original_source'] ?? '-';
        $info['create_time']=$return['structure']['create_time'] ?? '-';
        $aka=$return['structure']['aka'];
        if(!empty($aka)){
            $info['aka']=join(',',$aka);
        }else{
            $info['aka']='-';
        }
        $list=$child;
        unset($info['team_map']);

        $data = [
            'list' => $list,
            'info' => $info
        ];

        return $this->fetch('edit', $data);
    }

    


}