<?php
namespace app\common\validate;

use think\Validate;

class GameConfigValidate extends Validate
{
    protected $rule = [
        'title'          => 'require',
        'game' => 'require',
    ];

    protected $message = [
        'title.require'   => '请输入站点名称',
        'game.require' => '站点域名必须填写',
    ];

    protected $scene = [
        'create' => ['title', 'game'],
    ];
}