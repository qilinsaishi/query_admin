<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class ShangniuTournamentInfo extends BaseQueryList
{
    protected $pk = 'tournament_id';
	protected $table = 'shangniu_tournament_info';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'created_at';
    protected $updateTime = 'update_at';


    public function getList($request){
        $query = [];
        if (isset($request['query']['q']) && $request['query']['q'] !='') {

            if(is_numeric($request['query']['q'])){
                $request['query']['game']='';
                $request['params']['game']='';
                $query[] = ['tournament_id', 'eq', $request['query']['q']];
            }else{
                $query[] = ['tournament_name','like','%'.$request['query']['q'].'%'];
            }

        }

        if (!empty($request['query']['game'])) {
            $query[] = ['game', 'eq', $request['query']['game']];
        }
        
        
        // 分页参数
        $params = [];
        if (!empty($request['params'])) {
            $params = $request['params'];
        }
        $data=$this->where($query)
            ->order('tournament_id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        return $data;
    }


   
}