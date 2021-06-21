<?php
namespace app\common\validate\querylist;

use think\Validate;

class PlayerInfoValidate extends Validate
{
    protected $rule = [
        'player_name'   => 'require',
		'game'   => 'require',
        'logo'    => 'require',
		'team_id'   => 'require',
    ];

    protected $message = [
        'player_name.require' => '请输入队员名称',
		'game.require' => '请选择游戏类型',
        'logo.require'  => '请上传logo',
		'team_id.require' => '请选择所属战队',
    ];

    protected $scene = [
        'create'         => ['player_name','game','logo','team_id'],
        'edit'           => ['game','logo'],
    ];
}