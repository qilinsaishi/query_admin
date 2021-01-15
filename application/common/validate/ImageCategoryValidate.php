<?php
namespace app\common\validate;

use think\Validate;

class ImageCategoryValidate extends Validate
{
    protected $rule = [
        'name'   => 'require',
    ];

    protected $message = [
        'name.require'  => '请输入图片分类',
    ];

    protected $scene = [
        'create'         => ['name'],
        'edit'           => ['name'],
    ];
}