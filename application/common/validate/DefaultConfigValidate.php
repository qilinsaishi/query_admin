<?php
namespace app\common\validate;

use think\Validate;

class DefaultConfigValidate extends Validate
{
    protected $rule = [
        'name'   => 'require',
        'key'    => 'require',
		'value'    => 'require',
        'type'    => 'require',
    ];

    protected $message = [
        'name.require' => '请输入名称',
        'key.require'  => '请输入键名',
        'value.require'      => '请输入键值',
        'type.require'  => '请选择类型',
    ];

    protected $scene = [
        'create'         => ['name', 'key', 'value', 'type'],
        'edit'           => ['name', 'key', 'value', 'type'],
    ];
}