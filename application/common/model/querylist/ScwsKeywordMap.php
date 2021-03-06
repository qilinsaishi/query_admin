<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class ScwsKeywordMap extends BaseQueryList
{
    protected $pk = 'id';
	protected $table = 'scws_keyword_map';

   // protected $autoWriteTimestamp = true;

   // protected $createTime = 'create_time';
   // protected $updateTime = 'update_time';

    /*public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }*/
    public function getList($params,$q){
        $data=[];
        $data=$this->field('*')
            ->whereOr('keyword','like','%'.$q.'%')
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }

  


}