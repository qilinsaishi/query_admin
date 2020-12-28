<?php
namespace app\common\validate\querylist;

use think\Validate;

class CpseoTeamInfoValidate extends Validate
{
    protected $rule = [
        'team_name'   => 'require',
        'logo'    => 'require',
    ];

    protected $message = [
        'team_name.require' => '请输入名称',
        'logo.require'  => '请上传logo',
    ];

    protected $scene = [
        'create'         => ['team_name', 'logo'],
        'edit'           => [ 'logo'],
    ];
}