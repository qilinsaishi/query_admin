<?php
namespace app\common\validate\querylist;

use think\Validate;

class LolEquipmentInfoValidate extends Validate
{
    protected $rule = [
        'equipment_name'   => 'require',
		'cn_name'   => 'require',
		'price' 	=> 'require',
        'logo'    	=> 'require',
    ];

    protected $message = [
        'equipment_name.require' => '请输入装备名称',
		'cn_name.require' => '请输入中文名',
		'price.require' => '请输入价格',
        'logo.require'  => '请上传logo',
    ];

    protected $scene = [
        'create'         => ['equipment_name','cn_name','price','logo'],
        'edit'           => ['cn_name','price','logo'],
    ];
}