<?php
namespace app\common\model\querylist;

use app\common\model\querylist\TeamInfo as TeamInfoModel;
use think\Db;
use app\common\model\BaseQueryList;

class TeamList extends BaseQueryList
{
    protected $pk = 'tid';
	protected $table = 'team_list';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'created_at';
    protected $updateTime = 'update_at';


    public function getList($request){
        $query = [];
        if (isset($request['query']['q'])) {
            $map[]=['team_name|site_id|team_id|cn_name|en_name|location|tid','like','%'.$request['query']['q'].'%'];
            $teamInfo=new TeamInfo();
            $tids=$teamInfo->getIds($map);
            if(count($tids)>0){
                $query[] = ['tid','in',$tids];
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
            ->order('tid desc')
            ->paginate(20, false, [
                'type'     => 'bootstrap',
                'var_page' => 'page',
                'query'    => $params,
            ]);
        //print_r();exit;

        if(count($data)>0) {
            foreach ($data as &$val){
                $teamModel=new TeamInfo();
                $where['tid']=$val['tid'];
                $teamList=$teamModel->teamList($where,"*");
                $teamList=$teamList ?? [];
                $child=[];
                $postData=json_encode(['tid'=>$val['tid'],'type' => 'team']);
                $api_host=config('app.api_host').'/getIntergration';
                $return=curl_post($api_host, $postData);
                $return=json_decode($return,true);
                $val['team_name']=$return['data']['team_name'] ?? '-';
                $val['game']=$return['structure']['game'] ?? '-';
                $val['cn_name']=$return['structure']['cn_name'] ?? '-';
                $val['en_name']=$return['structure']['en_name'] ?? '-';
                $val['established_date']=$return['structure']['established_date'] ?? '-';
                $val['coach']=$return['structure']['coach'] ?? '-';
                $val['coach']=$return['structure']['coach'] ?? '-';
                $val['logo']=$return['structure']['logo'] ?? '-';
                $val['description']=$return['structure']['description'] ?? '-';
                $val['honor_list']=$return['structure']['honor_list'] ?? '-';
                $val['team_history']=$return['structure']['team_history'] ?? '-';
                $val['race_stat']=$return['structure']['race_stat'] ?? '-';
                $val['location']=$return['structure']['location'] ?? '-';
                $val['hot']=$return['structure']['hot'] ?? '-';
                $val['site_id']=$return['structure']['site_id'] ?? '-';
                $val['original_source']=$return['structure']['original_source'] ?? '-';
                $val['create_time']=$return['structure']['create_time'] ?? '-';
                $aka=$return['structure']['aka'];
                if(!empty($aka)){
                    $val['aka']=join(',',$aka);
                }else{
                    $val['aka']='-';
                }

                if(count($teamList)>0) {
                    foreach ($teamList as $k=>$v){
                        $child[$k]['team_name']=$v['team_name'] ?? '-';
                        $child[$k]['team_id']=$v['team_id'] ?? '-';
                        $child[$k]['en_name']=$v['en_name'] ?? '-';
                        $child[$k]['cn_name']=$v['cn_name'] ?? '-';
                        $child[$k]['established_date']=$v['established_date'] ?? '-';
                        $child[$k]['coach']=$v['coach'] ?? '-';
                        $child[$k]['logo']=$v['logo'] ?? '';
                        $child[$k]['location']=$v['location'] ?? '-';
                        $child[$k]['site_id']=$v['site_id'] ?? '-';
                        $child[$k]['game']=$v['game'] ?? '-';
                        $caka=json_decode($v['aka'],true);
                        if(is_array($caka) && count($caka)>0){
                            $child[$k]['aka']=join(',',$caka);
                        }else{
                            $child[$k]['aka']='-';
                        }

                        $child[$k]['original_source']=$v['original_source'] ?? '';
                        $child[$k]['create_time']=$v['create_time'] ?? '';
                        $child[$k]['update_time']=$v['update_time'] ?? '';
                        $child[$k]['tid']=$v['tid'] ?? '';

                    }

                }
                $val['child']=$child;

            }

        }
        return $data;
    }
    public function updateField($id,$data){
        return $this->where('tid',$id)->update($data);
    }






}