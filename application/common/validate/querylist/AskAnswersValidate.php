<?php
namespace app\common\validate\querylist;

use think\Validate;

class AskAnswersValidate extends Validate
{
    protected $rule = [
		'game_type'    	=> 'require',
        'ask'   => 'require',
        'answers'    	=> 'require',
		'sort'    	=> 'number',
    ];

    protected $message = [
		'game_type.require'  => '请输入游戏类型',
        'ask.require' => '请输入问题',
        'answers.require'  => '请输入答案',
		'sort.number'  => '排序必须是数字',
    ];

    protected $scene = [
        'create'         => ['game_type','ask','answers','sort'],
        'edit'           => ['game_type','ask','answers','sort'],
    ];
}