<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ImageCategory extends Model
{
    static public function getNameById($site_id,$game,$id)
    {
        $value='';
        $value = self::where(['site_id'=>$site_id,'game'=>$game])
            ->where('id', $id)
            ->value('name');
        return $value;
    }
    static public function getCategoryList($site_id, $game)
    {
        return self::where(['site_id'=>$site_id,'game'=>$game])
            ->order('sort asc')
            ->select();

    }
}