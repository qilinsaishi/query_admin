<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class KplInscriptionInfo extends BaseQueryList
{
    protected $pk = 'inscription_id';
	protected $table = 'kpl_inscription_info';

    protected $autoWriteTimestamp = true;

    //protected $createTime = 'create_time';
    //protected $updateTime = 'update_time';

    /*public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }*/
    public function getList($params,$q){
        $data=[];
        $data=$this->field('*')
            ->whereOr('inscription_name|cn_name|en_name','like','%'.$q.'%')
            ->order('inscription_id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }

  


}