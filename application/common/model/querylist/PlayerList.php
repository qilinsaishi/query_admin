<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class PlayerList extends BaseQueryList
{
    protected $pk = 'pid';
	protected $table = 'player_list';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /*public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }*/

    public function getList($request){
        $query = [];

        if (isset($request['query']['q'])) {
            $map[]=['player_name|position|en_name','like','%'.$request['query']['q'].'%'];
            $playerInfo=new PlayerInfo();
            $tids=$playerInfo->getIds($map);
            if(count($tids)>0){
                $query[] = ['pid','in',$tids];
            }
        }

        if (!empty($request['query']['game'])) {
            $query[] = ['game', 'eq', $request['query']['game']];
        }
        if (!empty($request['query']['original_source'])) {
            $query[] = ['original_source', 'eq', $request['query']['original_source']];
        }

        // 分页参数
        $params = [];
        if (!empty($request['params'])) {
            $params = $request['params'];
        }
        $data=$this->where($query)
            ->order('pid desc')
            ->with(['playerInfo'])
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);

        if(count($data)>0) {
            foreach ($data as &$val){
                $child=[];
                $postData=json_encode(['pid'=>$val['pid'],'type' => 'player']);
                $api_host=config('app.api_host').'/getIntergration';
                $return=curl_post($api_host, $postData);
                $return=json_decode($return,true);
                $playerInfo=$this->getPlayerInfoByPid($return['data']['pid'],'team_id');
                $val['team_id']=$playerInfo['team_id'];
                if(is_numeric($return['structure']['player_name'] ) && $return['structure']['player_name']>0){
                    $playerInfoName=$this->getPlayerInfo($return['structure']['player_name'],'player_name');
                    $val['player_name']=$playerInfoName['player_name'] ?? '';
                }else{
                    $val['player_name']=$return['data']['player_name'] ?? 0;
                }

                $val['player_id']=$return['structure']['player_id'] ?? 0;
                $val['game']=$return['structure']['game'] ?? '';
                $val['cn_name']=$return['structure']['cn_name'] ?? 0;
                $val['en_name']=$return['structure']['en_name'] ?? 0;
                $val['position']=$return['structure']['position'] ?? 0;
                $val['logo']=$return['structure']['logo'] ?? 0;
                $val['description']=$return['structure']['description'] ?? 0;
                $val['hot']=$return['structure']['hot'] ?? 0;
                $val['site_id']=$return['structure']['site_id'] ?? 0;
                $val['original_source']=$return['structure']['original_source'] ?? 0;
                $val['heros']=$return['structure']['heros'] ?? 0;
                $val['team_history']=$return['structure']['team_history'] ?? 0;
                $val['stat']=$return['structure']['stat'] ?? 0;
                $val['country']=$return['structure']['country'] ?? 0;
                $val['create_time']=$return['structure']['create_time'] ?? '';
                $aka=$return['structure']['aka'];
                if(!empty($aka)){
                    $val['aka']=join(',',$aka);
                }else{
                    $val['aka']='-';
                }
                if(count($val['playerInfo'])>0) {
                    foreach ($val['playerInfo'] as $k=>$v){
                        $child[$k]['player_name']=$v['player_name'] ?? '-';
                        $child[$k]['player_id']=$v['player_id'] ?? '-';
                        $child[$k]['team_id']=$v['team_id'] ?? '-';
                        $child[$k]['en_name']=$v['en_name'] ?? '-';
                        $child[$k]['cn_name']=$v['cn_name'] ?? '-';
                        $child[$k]['position']=$v['position'] ?? '-';
                        $child[$k]['stat']=$v['stat'] ?? '-';
                        $child[$k]['logo']=$v['logo'] ?? '';
                        $child[$k]['country']=$v['country'] ?? '-';
                        $child[$k]['site_id']=$v['site_id'] ?? '-';
                        $child[$k]['game']=$v['game'] ?? '-';
                        $child[$k]['original_source']=$v['original_source'] ?? '';
                        $child[$k]['create_time']=$v['create_time'] ?? '';
                        $child[$k]['update_time']=$v['update_time'] ?? '';
                        $child[$k]['pid']=$v['pid'] ?? '';

                        unset($v['info']);
                    }

                }
                $val['child']=$child;
                unset($val['playerInfo']);

            }

        }
        return $data;
    }

    public function updateField($id,$data){
        return $this->where('pid',$id)->update($data);
    }

    public function getPlayerInfo($id,$field="*"){
        return PlayerInfo::where('player_id',$id)->field($field)->find()->toArray();

    }
    public function getPlayerInfoByPid($pid,$field="*"){
        return PlayerInfo::where('pid',$pid)->field($field)->find()->toArray();

    }


    public function playerInfo(){
        //多对多
        //第一个参数是关联的模型，第二个参数是中间表(第三个表)的表名
        //第三个参数是关联模型的关联id，第4个参数当前模型的关联id
        return $this->hasMany('PlayerInfo','pid','pid');
    }





}