<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class KplHeroInfo extends BaseQueryList
{
    protected $pk = 'hero_id';
	protected $table = 'kpl_hero_info';

    protected $autoWriteTimestamp = true;

    //protected $createTime = 'create_time';
    //protected $updateTime = 'update_time';

    public function getList($request){
        $query = [];
        if (isset($request['query']['q'])) {
            $query[] = ['hero_name|cn_name','like','%'.$request['query']['q'].'%'];
        }

        if (!empty($request['query']['type'])) {
            $query[] = ['type', 'eq', $request['query']['type']];
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