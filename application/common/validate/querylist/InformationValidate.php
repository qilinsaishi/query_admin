<?php
namespace app\common\validate\querylist;

use think\Validate;

class InformationValidate extends Validate
{
    protected $rule = [
        'title'   => 'require',
        'logo'    	=> 'require',
    ];

    protected $message = [
        'title.require' => '请输入装备名称',
        'logo.require'  => '请上传logo',
    ];

    protected $scene = [
        'create'         => ['title','logo'],
        'edit'           => ['cn_name','logo'],
    ];
}