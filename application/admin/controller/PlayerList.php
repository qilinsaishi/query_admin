<?php
namespace app\admin\controller;

use app\common\model\Collect;
use app\common\model\querylist\ChangeLogs;
use app\common\model\querylist\CpseoTeamInfo as CpseoTeamInfoModel;
use app\common\model\querylist\PlayerList as PlayerListModel;
use app\common\model\querylist\PlayerInfo  as PlayerInfoModel;
use app\common\model\querylist\TeamList as TeamListModel;
use app\common\validate\querylist\PlayerInfoValidate;
use think\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Lang;
use think\facade\Config;
use app\common\model\Auth;
use app\admin\controller\Admin;


class PlayerList extends Admin
{
    public function lists()
    {
        $request = Request::param();
        $query = [
            'q'       => isset($request['q']) ? $request['q'] : '',
            'game'  => isset($request['game']) ? $request['game'] : '',
            'original_source'  => isset($request['original_source']) ? $request['original_source'] : '',
        ];
        $args = [
            'query'  => $query,
            'field'  => '',
            'order'  => 'pid desc',
            'params' => $query,
            'limit'  => 20,
        ];
        $gameList=config('app.game_type');
        $originalSource=config('app.original_source');
        // 分页列表
        $playInfoModel=new PlayerListModel();
        $list=$playInfoModel->getList($args);
        $this->assign('search', $query);
        $this->assign('list', $list);
        $this->assign('page', $list->render());
        $this->assign('gameList', $gameList);
        $this->assign('originalSource', $originalSource);
        return $this->fetch('lists');
    }

    public function selectHmtl(){
        $pid = Request::param('pid');
        $field = Request::param('field');
        $playerInfoModel = new PlayerInfoModel();
        $map['pid']=$pid;
        $teamInfos=$playerInfoModel->getFieldList($map,$field);
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
        $html.='<input type="hidden" name="pid" value="'.$pid.'">';
        $html.='<input type="hidden" name="field" value="'.$field.'">';
        return $this->response(200, Lang::get('Success'),$html);

    }
    public function updataList(){
        $request = Request::param();

        $pid=$request['pid'];
        unset($request['pid']);
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
            $playInfoModel=new PlayerListModel();
            $return=$playInfoModel->updateField($pid,$data);

            if ($return !== false) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }
        }else{
            return $this->response(201, '数据值不能为空');
        }


    }

    public function edit()
    {
        // 处理AJAX提交数据
        if (Request::isAjax()) {
            $request = Request::param();
            $pid=$request['pid'];
            unset($request['pid']);
            $playerInfoModel = new PlayerListModel();
            $return=$playerInfoModel->updateField($pid,$request);

            if ($return !== false) {
                return $this->response(200, Lang::get('Success'));
            } else {
                return $this->response(201, Lang::get('Fail'));
            }

        }

        $id = Request::param('id');
        $postData=json_encode(['pid'=>$id,'type' => 'player']);
        $api_host=config('app.api_host').'/getIntergration';
        $return=curl_post($api_host, $postData);
        $return=json_decode($return,true);
        $info = PlayerListModel::with(['playerInfo'])->get($id);

        $child=[];
        if(count($info['playerInfo'])>0) {
            foreach ($info['playerInfo'] as $k=>$v){
                $child[$k]['player_name']=$v['player_name'] ?? '-';
                $child[$k]['player_id']=$v['player_id'] ?? '-';
                $child[$k]['team_id']=$v['team_id'] ?? '-';
                $child[$k]['en_name']=$v['en_name'] ?? '-';
                $child[$k]['cn_name']=$v['cn_name'] ?? '-';
                $child[$k]['position']=$v['position'] ?? '-';
                $child[$k]['stat']=$v['stat'] ?? '-';
                $child[$k]['logo']=$v['logo'] ?? '';
                $child[$k]['country']=$v['country'] ?? '-';
                $child[$k]['site_id']=$v['site_id'] ?? '-';
                $child[$k]['game']=$v['game'] ?? '-';
                $child[$k]['description']=$v['description'] ?? '-';
                $child[$k]['honor_list']=$v['heros'] ?? [];
                $child[$k]['team_history']=$v['team_history'] ?? 0;
                $child[$k]['original_source']=$v['original_source'] ?? '';
                $child[$k]['create_time']=$v['create_time'] ?? '';
                $child[$k]['update_time']=$v['update_time'] ?? '';
                $child[$k]['pid']=$v['pid'] ?? '';

                unset($v['info']);
            }
        }
        $info['player_name']=$return['structure']['player_name'] ?? 0;
        $info['player_id']=$return['structure']['player_id'] ?? 0;
        $info['game']=$return['structure']['game'] ?? '';
        $info['cn_name']=$return['structure']['cn_name'] ?? 0;
        $info['en_name']=$return['structure']['en_name'] ?? 0;
        $info['position']=$return['structure']['position'] ?? 0;
        $info['team_id']=$return['structure']['team_id'] ?? 0;
        $info['logo']=$return['structure']['logo'] ?? 0;
        $info['description']=$return['structure']['description'] ?? 0;
        $info['hot']=$return['structure']['hot'] ?? 0;
        $info['site_id']=$return['structure']['site_id'] ?? 0;
        $info['original_source']=$return['structure']['original_source'] ?? 0;
        $info['heros']=$return['structure']['heros'] ?? 0;
        $info['team_history']=$return['structure']['team_history'] ?? 0;
        $info['stat']=$return['structure']['stat'] ?? 0;
        $info['country']=$return['structure']['country'] ?? 0;
        $info['create_time']=$return['structure']['create_time'] ?? '';
        $aka=$return['structure']['aka'];
        if(!empty($aka)){
            $info['aka']=join(',',$aka);
        }else{
            $info['aka']='-';
        }
        $list=$child;
        unset($info['playerMap']);
        unset($info['player_map']);

        $data = [
            'list' => $list,
            'info' => $info
        ];

        return $this->fetch('edit', $data);
    }



}