<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ImageList extends Model
{
    static public function getCategoryList($site_id, $game)
    {
        return self::where(['site_id'=>$site_id,'game'=>$game])
            ->order('sort asc')
            ->select();

    }
}