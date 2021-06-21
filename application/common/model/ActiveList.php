<?php
namespace app\common\model;

use think\Model;
use think\Db;

class ActiveList extends Model
{
    
    public function getInfoById($id)
    {
        return $this->where('id', $id)->find();
    }
}