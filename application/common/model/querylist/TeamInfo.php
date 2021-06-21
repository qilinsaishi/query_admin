<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class TeamInfo extends BaseQueryList
{
    protected $pk = 'team_id';
	protected $table = 'team_info';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'created_at';
    protected $updateTime = 'update_at';


    public function getList($request){


        $query = [];

        $is_intergrated=$request['query']['is_intergrated'] ??0;
        if($is_intergrated==1){//未整合
            $query[] = ['tid', 'eq', 0];
            $request['params'][]= ['tid', 'eq', 0];
            $request['query']['game']='';
            $request['params']['game']='';
        }elseif($is_intergrated==2){//已整合
            $query[] = ['tid', 'gt', 0];
            $request['params'][]=['tid', 'gt', 0];
        }

        unset($request['query']['is_intergrated']);
        unset($request['query']['params']);
        if (isset($request['query']['q'])) {
            if(is_numeric($request['query']['q'])){
                $request['query']['game']='';
                $request['params']['game']='';
                $query[] = ['site_id|team_id|tid', 'eq', $request['query']['q']];
            }else{
                $query[] = ['team_name|cn_name|en_name|location','like','%'.$request['query']['q'].'%'];
            }

        }

        if (!empty($request['query']['game'])) {
            $query[] = ['game', 'eq', $request['query']['game']];
        }
        if (!empty($request['query']['original_source'])) {
            $query[] = ['original_source', 'eq', $request['query']['original_source']];
        }

        // 分页参数
        $params = [];
        if (!empty($request['params'])) {
            $params = $request['params'];
        }
        $data=$this->where($query)
            ->order('team_id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }


    public function teamList($map,$filed="team_id,team_name,game,tid"){
        $data=[];
        $data=$this->where($map)->field($filed)->select()->toArray();
        return $data;
    }

    public function getTeamInfoByTeamId($team_id){
        return $this->get($team_id)->toArray();
    }

    public function getIds($map,$field='tid'){

        $data=$this->where($map)->column($field);
        return $data;
    }
    public function getFieldList($map,$field,$orderBy='team_id'){
        $data=[];
        $data=$this->where($map)->order($orderBy,'desc')->column($field,'team_id');
        return $data;
    }
    public function updateField($id,$data){
        return $this->where('team_id',$id)->update($data);
    }



}