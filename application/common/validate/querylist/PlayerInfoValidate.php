<?php
namespace app\common\validate\querylist;

use think\Validate;

class PlayerInfoValidate extends Validate
{
    protected $rule = [
        'player_name'   => 'require',
		'game'   => 'require',
		'position'   => 'require',
        'logo'    => 'require',
    ];

    protected $message = [
        'team_name.require' => '请输入名称',
		 'game.require' => '请选择游戏类型',
		 'position.require' => '请输入位置',
        'logo.require'  => '请上传logo',
    ];

    protected $scene = [
        'create'         => ['player_name','game', 'position','logo'],
        'edit'           => ['game','position','logo'],
    ];
}