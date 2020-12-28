<?php
namespace app\common\validate\querylist;

use think\Validate;

class LolHeroInfoValidate extends Validate
{
    protected $rule = [
        'hero_name'   => 'require',
        'logo'    => 'require',
    ];

    protected $message = [
        'hero_name.require' => '请输入英雄名称',
        'logo.require'  => '请上传logo',
    ];

    protected $scene = [
        'create'         => ['hero_name','logo'],
        'edit'           => ['logo'],
    ];
}