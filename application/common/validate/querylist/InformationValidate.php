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
        'title.require' => '请输入标题',
        'logo.require'  => '请上传缩略图',
    ];

    protected $scene = [
        'create'         => ['title','logo'],
        'edit'           => ['cn_name','logo'],
    ];
}