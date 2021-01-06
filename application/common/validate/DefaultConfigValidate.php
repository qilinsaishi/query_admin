<?php
namespace app\common\validate;

use think\Validate;

class DefaultConfigValidate extends Validate
{
    protected $rule = [
		'type'    => 'require',
        'name'   => 'require',
        'key'    => 'require',
		'value'    => 'require',
    ];

    protected $message = [
		'type.require'  => '请选择类型',
        'name.require' => '请输入名称',
        'key.require'  => '请输入键名',
        'value.require'      => '请输入键值',
    ];

    protected $scene = [
        'create'         => ['name', 'key', 'value', 'type'],
        'edit'           => ['name', 'key', 'value', 'type'],
    ];
}