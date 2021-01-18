<?php
namespace app\common\validate;

use think\Validate;

class ImageListValidate extends Validate
{
    protected $rule = [
		'logo'   => 'require',
        'name'   => 'require',
		'flag'   => 'require',
    ];

    protected $message = [
		'logo.require'  => '请上传logo',
        'name.require'  => '请输入名称',
		'flag.require'  => '请输入标记',
		
    ];

    protected $scene = [
        'create'         => ['logo','name','flag'],
        'edit'           => ['logo','name','flag'],
    ];
}