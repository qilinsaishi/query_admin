<?php
namespace app\common\model;

use think\Model;

class GameConfig extends Model
{
    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';

    public function getCreateAtAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getDefaultSite()
    {
        return GameConfig::where('id', '>', 0)->order('id', 'asc')->find();
    }
    public function getSiteNameById($id)
    {
        return GameConfig::where('id', '=', $id)->find();
    }

    public function getSiteList()
    {
        return GameConfig::all();
    }
}