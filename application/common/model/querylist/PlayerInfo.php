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

        if (isset($request['query']['q'])) {
            $query[] = ['player_name|position|en_name','like','%'.$request['query']['q'].'%'];
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
            ->order('player_id desc')
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
    public function getInfoList($map){
        $data=[];
        $data=$this->where($map)->select();
        return $data;
    }


    public function getFieldList($map,$field){
        $data=[];
        $data=$this->where($map)->column($field,'player_id');
        return $data;
    }




}