<?php
namespace app\common\model\querylist;

use think\Db;
use app\common\model\BaseQueryList;

class ChangeLogs extends BaseQueryList
{
    protected $pk = 'id';
	protected $table = 'change_logs';

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';

    static public function checkInsertData($modelClass,$data,$id,$type,$operator,$field='id'){

       //echo $id."\n"; print_r($modelClass);echo "\n";print_r($data);echo "\n";
        $model=new $modelClass;
        $old_data=$model->where($field,$id)->find();
        $cdata=[];
        $count=0;
        if(count($data)>0){
            foreach ($data as $k=>$v){
                if($v !=$old_data[$k]){
                    $cdata['old_content']=$old_data[$k];
                    $cdata['new_content']=$v;
                    $cdata['type']=$type;
                    $cdata['data_id']=$id;
                    $cdata['fields']=$k;
                    $cdata['create_time']=date("Y-m-d H:i:s");
                    $cdata['operator']=$operator;
                    $rt=self::insert($cdata);
                    if(!$rt)
                    {
                        return false;
                    }
                }

            }
        }
        return true;

    }


  


}