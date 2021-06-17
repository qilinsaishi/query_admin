<?php
namespace app\common\validate;

use think\Validate;

class ActiveListValidate extends Validate
{
    protected $rule = [
		'logo'   => 'require',
		'title'   => 'require',
		'start_time'=>'require',
		'end_time'=>'require',
		'site_id'=>'require',
		
    ];

    protected $message = [
		'logo.require'  => '请上传活动图片',
		'title.require'  => '请输入名称',
		'start_time.require'  => '请填写活动开始时间',
		'end_time.require'  => '请填写活动时间',
		'site_id.require'  => '请选择所属站点',
		
    ];

    protected $scene = [
        'create'         => ['title','logo','start_time','end_time','site_id'],
        'edit'           => ['title','logo'],
    ];
}