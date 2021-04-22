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

    public function getList($request){
        $query = [];
        $query[] = ['team_id', 'gt','0'];
        if (isset($request['query']['q'])) {
            $query[] = ['player_name|position|en_name|player_id|pid','like','%'.$request['query']['q'].'%'];
        }

        if (!empty($request['query']['game'])) {
            $query[] = ['game', 'eq', $request['query']['game']];
        }
        if (!empty($request['query']['original_source'])) {
            $query[] = ['original_source', 'eq', $request['query']['original_source']];
        }
        if (isset($request['query']['pid'])) {
            if($request['query']['pid']=='0'){
                $query[] = ['pid', 'eq', $request['query']['pid']];
            }elseif($request['query']['pid']=='1'){
                $query[] = ['pid', 'gt', 0];
            }

        }


        // 分页参数
        $params = [];
        if (!empty($request['params'])) {
            $params = $request['params'];
        }
        $data=$this->where($query)
            ->order('player_id desc')
            ->with(['teamInfo'=>function($teamInfo) {//这里需要加上use（where需要的条件）
                $teamInfo->where('tid','>','0' );
        }])
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        return $data;
    }
    public function getIds($map){
        $data=$this->where($map)->column('pid');
        return $data;
    }
    public function getInfoList($map){
        $data=[];
        $data=$this->where($map)->select();
        return $data;
    }

    public function teamInfo(){
        return $this->hasOne(TeamInfo::class,'team_id','team_id');
    }


    public function getFieldList($map,$field,$orderBy='team_id'){
        $data=[];
        $data=$this->where($map)->order($orderBy,'desc')->column($field,'player_id');
        return $data;
    }




}