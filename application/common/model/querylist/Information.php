<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class Information extends BaseQueryList
{
    protected $pk = 'id';
	protected $table = 'information';

    protected $autoWriteTimestamp = false;

    //protected $createTime = 'create_time';
    //protected $updateTime = 'update_time';

    public function getList($request){
        $query = [];
        if (isset($request['query']['q'])) {
            $query[] = ['title|author|id','like','%'.$request['query']['q'].'%'];
        }

        if (!empty($request['query']['type'])) {
            $query[] = ['type', 'eq', $request['query']['type']];
        }

        if (!empty($request['query']['game'])) {
            $query[] = ['game', 'eq', $request['query']['game']];
        }

        if (!empty($request['query']['source'])) {
            $query[] = [$request['query']['source'], 'eq', $request['query']['source']];
        }

        // 分页参数
        $params = [];
        if (!empty($request['params'])) {
            $params = $request['params'];
        }
        $data=$this->where($query)
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }

  


}