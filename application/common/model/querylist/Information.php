<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class Information extends BaseQueryList
{
    protected $pk = 'id';
	protected $table = 'information';

    protected $autoWriteTimestamp = true;

    //protected $createTime = 'create_time';
    //protected $updateTime = 'update_time';

    /*public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }*/
    public function getList($params,$q,$game){
        $data=[];
        $data=$this->field('*')
			->where('game',$game)
            ->whereOr('title|author','like','%'.$q.'%')
            ->order('id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }

  


}