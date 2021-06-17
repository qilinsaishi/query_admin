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
        if (isset($request['query']['q'])) {

            if(is_numeric($request['query']['q'])){
                $request['query']['game']='';
                $request['query']['original_source']='';
                $request['params']['game']='';
                $request['params']['original_source']='';
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
        if (isset($request['query']['tid'])) {
            if($request['query']['tid']=='0' || $request['query']['tid']==0){
                $query[] = ['tid', 'eq', $request['query']['tid']];
            }elseif($request['query']['tid']=='1' || $request['query']['tid']==1){
                $query[] = ['tid', 'gt', $request['query']['tid']];
            }

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