<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 驱动方式
    'type'   => 'File',
    // 缓存保存目录
    'path'   => '',
    // 缓存前缀
    'prefix' => '',
    // 缓存有效期 0表示永久缓存
    'expire' => 0,
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    'name'    => 'Think Console',
    'version' => '0.1',
    'user'    => null,
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Cookie设置
// +----------------------------------------------------------------------
return [
    // cookie 名称前缀
    'prefix'    => '',
    // cookie 保存时间
    'expire'    => 0,
    // cookie 保存路径
    'path'      => '/',
    // cookie 有效域名
    'domain'    => '',
    //  cookie 启用安全传输
    'secure'    => false,
    // httponly设置
    'httponly'  => '',
    // 是否使用 setcookie
    'setcookie' => true,
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'kitecms',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => 'root',
    // 端口
    'hostport'        => '3306',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => 'kite_',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\think\db\Query',
    // 是否需要断线重连
    'break_reconnect' => false,
    // 断线标识字符串
    'break_match_str' => [],
];<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'kitecms',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => 'root',
    // 端口
    'hostport'        => '3306',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => 'kite_',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\think\db\Query',
    // 是否需要断线重连
    'break_reconnect' => false,
    // 断线标识字符串
    'break_match_str' => [],
];<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 日志记录方式，内置 file socket 支持扩展
    'type'        => 'File',
    // 日志保存目录
    'path'        => '',
    // 日志记录级别
    'level'       => [],
    // 单文件日志写入
    'single'      => false,
    // 独立日志级别
    'apart_level' => [],
    // 最大日志文件数量
    'max_files'   => 0,
    // 是否关闭日志写入
    'close'       => false,
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 中间件配置
// +----------------------------------------------------------------------
return [
    // 默认中间件命名空间
    'default_namespace' => 'app\\http\\middleware\\',
];<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

return [
    'id'             => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix'         => 'think',
    // 驱动方式 支持redis memcache memcached
    'type'           => '',
    // 是否自动开启 SESSION
    'auto_start'     => true,
];
<?php

/**
 * 配置说明
 * 系统运行使用的配置存在于数据库 ："prefix_site_config" 表中。
 * 关于该文件配置的作用为默认配置，表中没有配置项时候，会读取该文件配置，基于此配置创建配置项。
 * 所以，您需要更改配置请到系统后台进行修改，修改此配置仅在数据表中没有配置项时候生效。
 */
return [

    // base
    'base' => [
        'member_allow_register' => 1,
        'member_allow_comment'  => 1,
        'list_rows'             => 15,
        'default_role'          => 3,
    ],


    // 支付宝
    'payment' => [
        'ali_appid'       => '1',
        'ali_private_key' => '1',
        'ali_public_key'  => '1',
        'ali_notify_url'  => '1',
        'ali_return_url'  => '1',

        'wx_appid'       => '1',
        'wx_mch_id'      => '1',
        'wx_notify_url'  => '1',
        'wx_key'         => '1',
    ],


    // 字段类型
    'filedType' => [
        'text'                => '字符串(text)',
        'textarea'            => '文本框(textArea)',
        'radio'               => '单选(radio)',
        'checkbox'            => '多选(checkBox)',
        'select'              => '下拉选择框(select)',
        'datetime'            => '日期(dateTime)',
        'imageupload'         => '图片上传(imageUpload)',
        'multipleimageupload' => '多图片上传(multipleImageUpload)',
        'videoupload'         => '视频上传(videoUpload)',
        'attachupload'        => '附件上传(attachUpload)',
    ],

    // 邮箱
    'email' => [
        'email_host'       => 'smtp.163.com',      // 邮件服务器
        'email_port'       => '465',               // 邮件服务器端口
        'email_username'   => 'kite365',           // 发信人邮件账户
        'email_password'   => 'wangzheng',         // 发信人邮件密码
        'email_from_email' => 'kite@kitesky.com',  // 发信人邮件密码
        'email_from_name'  => 'KiteCMS',           // 发信人邮件密码

        // 邮件模版
        'email_code_template'     => '尊敬的会员${username} ，您本次的验证码为：${code} ，验证码在5分钟内有效。',               // 验证码模版
        'email_register_template' => '尊敬的会员${username} ，您已经成功注册，请谨记您的用户名及密码。',               // 注册成功公职模版

        // 邮件通知选项
        'send_email_register'     => 0                     // 注册发送邮件
    ],

    // SMS短信接口
    'sms' => [
        'sms_type'              => 'dysms',             // 接口 dysms 阿里大鱼
        'ali_access_key'        => 'AccessKey ID',      // AccessKey ID
        'ali_access_key_secret' => 'Access Key Secret', // Access Key Secret
        'ali_sign_name'         => '19981.com',         // 签名
        'ali_template_code'     => 'SMS_1234',          // 模板代码
    ],

    // 文档选项属性
    'document_option' => [
        'image_flag'     => 'Image',
        'video_flag'     => 'Video',
        'attach_flag'    => 'Attach',
        'hot_flag'       => 'Hot',
        'recommend_flag' => 'Recommend',
        'focus_flag'     => 'Focus',
        'top_flag'       => 'Top',
    ],

    // 验证码默认配置
    'captcha' => [
        'captcha_useZh'            => 0,
        'captcha_useImgBg'         => 0,
        'captcha_fontSize'         => 14,
        'captcha_imageH'           => 0,
        'captcha_imageW'           => 0,
        'captcha_length'           => 6,
        'captcha_useCurve'         => 0,
        'captcha_useNoise'         => 0,
        'captcha_member_register'  => 0,
        'captcha_member_login'     => 0,
        'captcha_publish_document' => 0,
        'captcha_publish_comment'  => 0,
        'captcha_publish_feedback' => 0,
    ],

    // 图片水印默认配置
    'imageWater' => [
        'water_logo'     => '/static/admin/dist/img/nopic.png',
        'water_position' => 9,
        'water_quality'  => 80,
        'water_status'   => 0,
    ],

    // 上传图片默认配置
    'uploadFile' => [
        'upload_type'        => 'local',
        'upload_image_ext'   => 'jpg,png,gif',
        'upload_image_size'  => '2040000',
        'upload_video_ext'   => 'rm,rmvb,wmv,3gp,mp4,mov,avi,flv',
        'upload_video_size'  => '2040000',
        'upload_attach_ext'  => 'doc,xls,rar,zip',
        'upload_attach_size' => '2040000',
        'upload_path'        => 'upload',

        'alioss_key'      => '4H5C4jQbxBAsbwye',
        'alioss_secret'   => 'U5Be9VLZCpy8oCo7sTQSCq806swqGV',
        'alioss_endpoint' => 'oss-cn-shenzhen.aliyuncs.com',
        'alioss_bucket'   => 'kitesky',

        'qiniu_ak'        => '9VWzf1jiS3gEALBi_XtwELHaHzHJIeCXE5W4KtJt',
        'qiniu_sk'        => 'yHGWn3FwN37fkRWpOzzMhXC5jEfgr8',
        'qiniu_bucket'    => 'kitesky',
        'qiniu_domain'    => 'http://onxr8mt8y.bkt.clouddn.com',
    ],

    // 会员积分策略配置
    'memberScore' => [
        'register_score' => 100,
        'login_score'    => 1,
        'publish_score'  => 10,
        'comment_score'  => 5,
    ],

    // upload根目录
    'public_path' => Env::get('root_path'),
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
    'auto_rule'    => 1,
    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end'   => '}',
    'default_filter' => '',
    'tpl_replace_string' => [
        '__STATIC__' => '/public/static',
        '__THEME__'  => '/theme',
    ],
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Trace设置 开启 app_trace 后 有效
// +----------------------------------------------------------------------
return [
    // 内置Html Console 支持扩展
    'type' => 'Html',
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    'name'    => 'Think Console',
    'version' => '0.1',
    'user'    => null,
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Cookie设置
// +----------------------------------------------------------------------
return [
    // cookie 名称前缀
    'prefix'    => '',
    // cookie 保存时间
    'expire'    => 0,
    // cookie 保存路径
    'path'      => '/',
    // cookie 有效域名
    'domain'    => '',
    //  cookie 启用安全传输
    'secure'    => false,
    // httponly设置
    'httponly'  => '',
    // 是否使用 setcookie
    'setcookie' => true,
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'kitecms',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => 'root',
    // 端口
    'hostport'        => '3306',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => 'kite_',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\think\db\Query',
    // 是否需要断线重连
    'break_reconnect' => false,
    // 断线标识字符串
    'break_match_str' => [],
];<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '127.0.0.1',
    // 数据库名
    'database'        => 'kitecms',
    // 用户名
    'username'        => 'root',
    // 密码
    'password'        => 'root',
    // 端口
    'hostport'        => '3306',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => 'kite_',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\think\db\Query',
    // 是否需要断线重连
    'break_reconnect' => false,
    // 断线标识字符串
    'break_match_str' => [],
];<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------
return [
    // 日志记录方式，内置 file socket 支持扩展
    'type'        => 'File',
    // 日志保存目录
    'path'        => '',
    // 日志记录级别
    'level'       => [],
    // 单文件日志写入
    'single'      => false,
    // 独立日志级别
    'apart_level' => [],
    // 最大日志文件数量
    'max_files'   => 0,
    // 是否关闭日志写入
    'close'       => false,
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 中间件配置
// +----------------------------------------------------------------------
return [
    // 默认中间件命名空间
    'default_namespace' => 'app\\http\\middleware\\',
];<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

return [
    'id'             => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix'         => 'think',
    // 驱动方式 支持redis memcache memcached
    'type'           => '',
    // 是否自动开启 SESSION
    'auto_start'     => true,
];
<?php

/**
 * 配置说明
 * 系统运行使用的配置存在于数据库 ："prefix_site_config" 表中。
 * 关于该文件配置的作用为默认配置，表中没有配置项时候，会读取该文件配置，基于此配置创建配置项。
 * 所以，您需要更改配置请到系统后台进行修改，修改此配置仅在数据表中没有配置项时候生效。
 */
return [

    // base
    'base' => [
        'member_allow_register' => 1,
        'member_allow_comment'  => 1,
        'list_rows'             => 15,
        'default_role'          => 3,
    ],


    // 支付宝
    'payment' => [
        'ali_appid'       => '1',
        'ali_private_key' => '1',
        'ali_public_key'  => '1',
        'ali_notify_url'  => '1',
        'ali_return_url'  => '1',

        'wx_appid'       => '1',
        'wx_mch_id'      => '1',
        'wx_notify_url'  => '1',
        'wx_key'         => '1',
    ],


    // 字段类型
    'filedType' => [
        'text'                => '字符串(text)',
        'textarea'            => '文本框(textArea)',
        'radio'               => '单选(radio)',
        'checkbox'            => '多选(checkBox)',
        'select'              => '下拉选择框(select)',
        'datetime'            => '日期(dateTime)',
        'imageupload'         => '图片上传(imageUpload)',
        'multipleimageupload' => '多图片上传(multipleImageUpload)',
        'videoupload'         => '视频上传(videoUpload)',
        'attachupload'        => '附件上传(attachUpload)',
    ],

    // 邮箱
    'email' => [
        'email_host'       => 'smtp.163.com',      // 邮件服务器
        'email_port'       => '465',               // 邮件服务器端口
        'email_username'   => 'kite365',           // 发信人邮件账户
        'email_password'   => 'wangzheng',         // 发信人邮件密码
        'email_from_email' => 'kite@kitesky.com',  // 发信人邮件密码
        'email_from_name'  => 'KiteCMS',           // 发信人邮件密码

        // 邮件模版
        'email_code_template'     => '尊敬的会员${username} ，您本次的验证码为：${code} ，验证码在5分钟内有效。',               // 验证码模版
        'email_register_template' => '尊敬的会员${username} ，您已经成功注册，请谨记您的用户名及密码。',               // 注册成功公职模版

        // 邮件通知选项
        'send_email_register'     => 0                     // 注册发送邮件
    ],

    // SMS短信接口
    'sms' => [
        'sms_type'              => 'dysms',             // 接口 dysms 阿里大鱼
        'ali_access_key'        => 'AccessKey ID',      // AccessKey ID
        'ali_access_key_secret' => 'Access Key Secret', // Access Key Secret
        'ali_sign_name'         => '19981.com',         // 签名
        'ali_template_code'     => 'SMS_1234',          // 模板代码
    ],

    // 文档选项属性
    'document_option' => [
        'image_flag'     => 'Image',
        'video_flag'     => 'Video',
        'attach_flag'    => 'Attach',
        'hot_flag'       => 'Hot',
        'recommend_flag' => 'Recommend',
        'focus_flag'     => 'Focus',
        'top_flag'       => 'Top',
    ],

    // 验证码默认配置
    'captcha' => [
        'captcha_useZh'            => 0,
        'captcha_useImgBg'         => 0,
        'captcha_fontSize'         => 14,
        'captcha_imageH'           => 0,
        'captcha_imageW'           => 0,
        'captcha_length'           => 6,
        'captcha_useCurve'         => 0,
        'captcha_useNoise'         => 0,
        'captcha_member_register'  => 0,
        'captcha_member_login'     => 0,
        'captcha_publish_document' => 0,
        'captcha_publish_comment'  => 0,
        'captcha_publish_feedback' => 0,
    ],

    // 图片水印默认配置
    'imageWater' => [
        'water_logo'     => '/static/admin/dist/img/nopic.png',
        'water_position' => 9,
        'water_quality'  => 80,
        'water_status'   => 0,
    ],

    // 上传图片默认配置
    'uploadFile' => [
        'upload_type'        => 'local',
        'upload_image_ext'   => 'jpg,png,gif',
        'upload_image_size'  => '2040000',
        'upload_video_ext'   => 'rm,rmvb,wmv,3gp,mp4,mov,avi,flv',
        'upload_video_size'  => '2040000',
        'upload_attach_ext'  => 'doc,xls,rar,zip',
        'upload_attach_size' => '2040000',
        'upload_path'        => 'upload',

        'alioss_key'      => '4H5C4jQbxBAsbwye',
        'alioss_secret'   => 'U5Be9VLZCpy8oCo7sTQSCq806swqGV',
        'alioss_endpoint' => 'oss-cn-shenzhen.aliyuncs.com',
        'alioss_bucket'   => 'kitesky',

        'qiniu_ak'        => '9VWzf1jiS3gEALBi_XtwELHaHzHJIeCXE5W4KtJt',
        'qiniu_sk'        => 'yHGWn3FwN37fkRWpOzzMhXC5jEfgr8',
        'qiniu_bucket'    => 'kitesky',
        'qiniu_domain'    => 'http://onxr8mt8y.bkt.clouddn.com',
    ],

    // 会员积分策略配置
    'memberScore' => [
        'register_score' => 100,
        'login_score'    => 1,
        'publish_score'  => 10,
        'comment_score'  => 5,
    ],

    // upload根目录
    'public_path' => Env::get('root_path'),
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型 支持 php think 支持扩展
    'type'         => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
    'auto_rule'    => 1,
    // 模板路径
    'view_path'    => '',
    // 模板后缀
    'view_suffix'  => 'html',
    // 模板文件名分隔符
    'view_depr'    => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'    => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'      => '}',
    // 标签库标签开始标记
    'taglib_begin' => '{',
    // 标签库标签结束标记
    'taglib_end'   => '}',
    'default_filter' => '',
    'tpl_replace_string' => [
        '__STATIC__' => '/public/static',
        '__THEME__'  => '/theme',
    ],
];
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | Trace设置 开启 app_trace 后 有效
// +----------------------------------------------------------------------
return [
    // 内置Html Console 支持扩展
    'type' => 'Html',
];
