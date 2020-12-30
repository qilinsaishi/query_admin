<?php
namespace app\common\validate\querylist;

use think\Validate;

class LolSummonerInfoValidate extends Validate
{
    protected $rule = [
        'skill_name'   => 'require',
		'cn_name'   => 'require',
        'logo'    	=> 'require',
		'rank'    => 'require|number',
    ];

    protected $message = [
        'skill_name.require' => '请输入装备名称',
		'cn_name.require' => '请输入中文名',
        'logo.require'  => '请上传logo',
		'rank.require'   => '请填写等级',
        'rank.number'    => '等级必须是数字',
    ];

    protected $scene = [
        'create'         => ['skill_name','cn_name','rank','logo'],
        'edit'           => ['cn_name','rank','logo'],
    ];
}