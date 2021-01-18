<?php
namespace app\common\validate;

use think\Validate;

class DefaultConfigValidate extends Validate
{
    protected $rule = [
		'type'    => 'require',
        'name'   => 'require',
		'site_id'    => 'require',
        'key'    => 'require',
		'value'    => 'require',
    ];

    protected $message = [
		'type.require'  => '请选择类型',
        'name.require' => '请输入名称',
		'site_id.require' => '请选择所属站点',
        'key.require'  => '请输入键名',
        'value.require'      => '请输入键值',
    ];

    protected $scene = [
        'create'         => ['name', 'key', 'value', 'type','site_id'],
        'edit'           => ['name', 'key', 'value', 'type','site_id'],
    ];
}