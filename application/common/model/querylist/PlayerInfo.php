<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class PlayerInfo extends BaseQueryList
{
    protected $pk = 'player_id';
	protected $table = 'player_info';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'created_at';
    protected $updateTime = 'update_at';

    /*public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }*/

    public function getList($request){
        $query = [];
        $is_intergrated=$request['query']['is_intergrated'] ??0;
        if($is_intergrated==1){//未整合
            $query[] = ['pid', 'eq', 0];
            $request['params'][]= ['pid', 'eq', 0];
            $request['query']['game']='';
            $request['query']['original_source']='';
            $request['params']['game']='';
            $request['params']['original_source']='';
        }elseif($is_intergrated==2){//已整合
            $query[] = ['pid', 'gt', 0];
            $request['params'][]=['pid', 'gt', 0];
        }

        $query[] = ['team_id', 'gt','0'];
        if (isset($request['query']['q'])) {
            if(is_numeric($request['query']['q'])){
                $query[] = ['player_id|pid|team_id','eq',$request['query']['q']];
                $request['query']['game']='';
                $request['params']['game']='';
            }else{
                $query[] = ['player_name|position|en_name','like','%'.$request['query']['q'].'%'];
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
        $teamInfoModel=new TeamInfo();
        $data=$this->where($query)
            ->order('player_id desc')
            ->whereIn('team_id',$teamInfoModel->getIds([],"team_id"))
            //->whereIn('original_source',config('app.original_source'))
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        return $data;
    }
    public function getIds($map){
        $data=$this->where($map)->column('pid');
        return $data;
    }

    public function teamInfo(){
        return $this->hasOne(TeamInfo::class,'team_id','team_id');
    }

    //public function getFieldList($map,$field,$orderBy='team_id',$teamIds=[]){

    public function getFieldList($map,$field,$orderBy='team_id',$teamIds=[]){
        $data=[];
        $builder=$this->where($map)->order($orderBy,'desc');
        if(count($teamIds) >0){
            $builder=$builder->whereIn('team_id',$teamIds);
        }

        $data=$builder->column($field,'player_id');

        return $data;
    }




}