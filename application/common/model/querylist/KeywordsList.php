<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class KeywordsList extends BaseQueryList
{
    protected $pk = 'id';
	protected $table = 'keywords_list';

    //protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /*public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }*/
    public function getList($params,$q){
        $data=[];
        $data=$this->field('*')
            ->whereOr('word','like','%'.$q.'%')
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }

  


}