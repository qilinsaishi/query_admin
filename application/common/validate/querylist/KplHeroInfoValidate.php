<?php
namespace app\common\validate\querylist;

use think\Validate;

class KplHeroInfoValidate extends Validate
{
    protected $rule = [
        'hero_name'   => 'require',
		'type'    => 'require',
        'logo'    => 'require',
    ];

    protected $message = [
        'hero_name.require' => '请输入英雄名称',
		'type.require' => '请选择类型',
        'logo.require'  => '请上传logo',
    ];

    protected $scene = [
        'create'         => ['hero_name','type','logo'],
        'edit'           => ['type','logo'],
    ];
}