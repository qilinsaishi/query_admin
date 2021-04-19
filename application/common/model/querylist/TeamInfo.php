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
            $query[] = ['team_name|site_id|team_id|cn_name|en_name|location|tid','like','%'.$request['query']['q'].'%'];
        }

        if (!empty($request['query']['game'])) {
            $query[] = ['game', 'eq', $request['query']['game']];
        }
        if (!empty($request['query']['original_source'])) {
            $query[] = ['original_source', 'eq', $request['query']['original_source']];
        }
        if (isset($request['query']['tid'])) {
            if($request['query']['tid']=='0'){
                $query[] = ['tid', 'eq', $request['query']['tid']];
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

    public function teamList($map,$filed="team_id,team_name,game"){
        $data=[];
        $data=$this->where($map)->field($filed)->select()->toArray();
        return $data;
    }

    public function getIds($map){
        $data=$this->where($map)->column('tid');
        return $data;
    }
    public function getFieldList($map,$field){
        $data=[];
        $data=$this->where($map)->column($field,'team_id');
        return $data;
    }
    public function updateField($id,$data){
        return $this->where('team_id',$id)->update($data);
    }



}