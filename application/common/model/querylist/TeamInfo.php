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

    /*public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }*/
    public function getList($params,$q){
        $data=[];
        $data=$this->field('*')
            ->whereOr('team_name','like','%'.$q.'%')
            ->order('team_id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }

    public function teamList($game=''){
        $data=[];
        $data=$this->where('game',$game)->field("team_id,team_name,game")->select()->toArray();
        return $data;
    }

  


}