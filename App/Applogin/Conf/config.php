<?php

return [
    /*     * ***********************************附加设置********************************** */
    'SHOW_PAGE_TRACE' => false, // 是否显示调试面板
    'URL_CASE_INSENSITIVE' => false, // url区分大小写
    'TAGLIB_BUILD_IN' => 'Cx,Applogin\Tag\My', // 加载自定义标签
    'LOAD_EXT_CONFIG' => 'db', // 加载网站设置文件
    'TMPL_PARSE_STRING' => [// 定义常用路径
        '__PUBLIC__' => __ROOT__ . '/Public/Default',
        '__ADMIN_ACEADMIN__' => __ROOT__ . '/Public/Default/aceadmin',
    ],
    /*     * *********************************auth设置********************************* */
    'AUTH_CONFIG' => [
        'AUTH_USER' => 'admin_users'                         //用户信息表
    ],
    /*     * *********************************邮件服务器********************************* */
    'EMAIL_FROM_NAME' => '', // 发件人
    'EMAIL_SMTP' => '', // smtp
    'EMAIL_USERNAME' => '', // 账号
    'EMAIL_PASSWORD' => '', //密码
    /*     * *********************************其他配置********************************* */
    'NEED_UPLOAD_OSS' => [ // 需要上传的目录
        '/Public/Upload/avatar',
        '/Public/Upload/cover',
        '/Public/Upload/image/webuploader',
        '/Public/Upload/video',
    ],
    'DEFAULT_PAGE_SIZE' => 20,
    'UP_IMG_CONFIG' => array(
        'mimes'         =>  array(), //允许上传的文件MiMe类型
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array(), //允许上传的文件后缀
        'autoSub'       =>  true, //自动子目录保存文件
        'subName'       =>  array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'      =>  './Public/Default/home/upload/', //保存根路径
        'savePath'      =>  'product/', //保存路径
        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'       =>  'jpg', //文件保存后缀，空则使用原后缀
        'replace'       =>  true, //存在同名是否覆盖
        'hash'          =>  true, //是否生成hash编码
        'callback'      =>  false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver'        =>  '', // 文件上传驱动
        'driverConfig'  =>  array(), // 上传驱动配置
        'dbPath'		=> 	'/Public/Default/home/upload/',	// 数据库路径
    ),
];
