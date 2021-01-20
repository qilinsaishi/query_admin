<?php
namespace app\common\validate\querylist;

use think\Validate;

class KeywordsListValidate extends Validate
{
    protected $rule = [
		'word'    	=> 'require',
        'url'   => 'require|url',
    ];

    protected $message = [
		'word.require'  => '请输入关键字',
        'url.require' => '请输入跳转链接',
        'url.url'  => '跳转链接格式错误',
    ];

    protected $scene = [
        'create'         => ['word','url'],
        'edit'           => ['word','url'],
    ];
}