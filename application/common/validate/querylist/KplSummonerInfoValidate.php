<?php
namespace app\common\validate\querylist;

use think\Validate;

class KplSummonerInfoValidate extends Validate
{
    protected $rule = [
        'skill_name'   => 'require',
		'cn_name'   => 'require',
        'logo'    	=> 'require',
    ];

    protected $message = [
        'skill_name.require' => '请输入装备名称',
		'cn_name.require' => '请输入中文名',
        'logo.require'  => '请上传logo',
    ];

    protected $scene = [
        'create'         => ['skill_name','cn_name','logo'],
        'edit'           => ['cn_name','logo'],
    ];
}