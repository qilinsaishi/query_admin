<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class ShangniuMatchList extends BaseQueryList
{
    protected $pk = 'match_id';
	protected $table = 'shangniu_match_list';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'created_at';
    protected $updateTime = 'update_at';


    public function getList($request){
        $query = [];
        if (isset($request['query']['q'])) {

            if(is_numeric($request['query']['q'])){
               // $request['query']['game']='';
               // $request['params']['game']='';
                $query[] = ['match_id|home_id|away_id', 'eq', $request['query']['q']];
            }else{
                $query[] = ['round_detailed','like','%'.$request['query']['q'].'%'];
            }

        }

        if (!empty($request['query']['game'])) {
            $query[] = ['game', 'eq', $request['query']['game']];
        }
        if ($request['query']['match_status']>-1) {
            $query[] = ['match_status', 'eq', $request['query']['match_status']];
        }

        $filed="match_id,game,match_status,round_detailed,game_bo,tournament_id,home_id,away_id,
        home_display,away_display,home_score,away_score,start_time,update_time,create_time,next_try,try";
        // 分页参数
        $params = [];
        if (!empty($request['params'])) {
            $params = $request['params'];
        }
        $data=$this->where($query)
            ->field($filed)
            ->order('match_id desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        return $data;
    }
    public function MatchCount($map){
        return $this->where($map)->count();
    }








   
}