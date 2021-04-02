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
           // $query[] = ['player_name|position|en_name','like','%'.$request['query']['q'].'%'];
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
            ->with(['playerMap.info'])
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
                $val['player_name']=$return['structure']['player_name'] ?? 0;
                $val['player_id']=$return['structure']['player_id'] ?? 0;
                $val['game']=$return['structure']['game'] ?? '';
                $val['cn_name']=$return['structure']['cn_name'] ?? 0;
                $val['en_name']=$return['structure']['en_name'] ?? 0;
                $val['position']=$return['structure']['position'] ?? 0;
                $val['team_id']=$return['structure']['team_id'] ?? 0;
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
                if(count($val['player_map'])>0) {
                    foreach ($val['player_map'] as $k=>$v){
                        $child[$k]['player_name']=$v['info']['player_name'] ?? '-';
                        $child[$k]['player_id']=$v['info']['player_id'] ?? '-';
                        $child[$k]['team_id']=$v['info']['team_id'] ?? '-';
                        $child[$k]['en_name']=$v['info']['en_name'] ?? '-';
                        $child[$k]['cn_name']=$v['info']['cn_name'] ?? '-';
                        $child[$k]['position']=$v['info']['position'] ?? '-';
                        $child[$k]['stat']=$v['info']['stat'] ?? '-';
                        $child[$k]['logo']=$v['info']['logo'] ?? '';
                        $child[$k]['country']=$v['info']['country'] ?? '-';
                        $child[$k]['site_id']=$v['info']['site_id'] ?? '-';
                        $child[$k]['game']=$v['info']['game'] ?? '-';
                        $child[$k]['original_source']=$v['info']['original_source'] ?? '';
                        $child[$k]['create_time']=$v['info']['create_time'] ?? '';
                        $child[$k]['update_time']=$v['info']['update_time'] ?? '';
                        $child[$k]['pid']=$v['info']['pid'] ?? '';

                        unset($v['info']);
                    }

                }
                $val['child']=$child;
                unset($val['player_map']);

            }

        }
        return $data;
    }

    public function updateField($id,$data){
        return $this->where('pid',$id)->update($data);
    }


    public function playerMap(){
        //多对多
        //第一个参数是关联的模型，第二个参数是中间表(第三个表)的表名
        //第三个参数是关联模型的关联id，第4个参数当前模型的关联id
        return $this->hasMany('PlayerMap','pid','pid');
    }





}