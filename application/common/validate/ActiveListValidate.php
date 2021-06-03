<?php
namespace app\common\validate;

use think\Validate;

class ActiveListValidate extends Validate
{
    protected $rule = [
		'title'   => 'require',
		'logo'   => 'require',
    ];

    protected $message = [
		'title.require'  => '请输入名称',
		'logo.require'  => '请上传活动图片',
    ];

    protected $scene = [
        'create'         => ['title','logo'],
        'edit'           => ['title','logo'],
    ];
}