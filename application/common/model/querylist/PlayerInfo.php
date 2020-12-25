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
    public function getList($params,$q){
        $data=[];
        $data=$this->field('*')
            ->whereOr('player_name|cn_name|en_name|position','like','%'.$q.'%')
            ->order('player_id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }

  


}