<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class CpseoTeamInfo extends BaseQueryList
{
    protected $pk = 'team_id';
	protected $table = 'team_info';

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
            $query[] = ['team_name','like','%'.$request['query']['q'].'%'];
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

  


}