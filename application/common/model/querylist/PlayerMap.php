<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class PlayerMap extends BaseQueryList
{
    protected $pk = 'id';
	protected $table = 'player_info';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'created_at';
    protected $updateTime = 'update_at';

    public function info(){
        //多对多
        //第一个参数是关联的模型，第二个参数是中间表(第三个表)的表名
        //第三个参数是关联模型的关联id，第4个参数当前模型的关联id
        return $this->belongsTo('PlayerInfo','player_id','player_id');
    }



}