<?php

return [
    'FRAMEWORK_VERSION' => ' V0.6 ',
    'DOMAIN_NAME_URL' => 'http://',
    'LOAD_EXT_CONFIG' => 'wechat,db,rsa',
    'DATE_FORMAT'=>'Y-m-d H:i:s',
//***********************************URL设置**************************************
    //'MODULE_ALLOW_LIST' => ['Home', 'Backstage', 'Login'], //允许访问列表
    'MODULE_DENY_LIST' => ['Common'], // 禁止访问的模块列表
    'URL_MODEL' => 2, //URL模式
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符
    'URL_ROUTER_ON' => true, // url路由开关
    'URL_ROUTE_RULES' =>array(
        '/^([a-zA-Z0-9-]+)$/'=>'List/html?url=:1',
        '/^([a-zA-Z0-9-]+)_(\d+)$/'=>'List/html?url=:1&p=:2'
    ),
 //************************************缓存设置*********************************
//    'REDIS_HOST'                        => '127.0.0.1',         //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
//    'REDIS_PORT'                        => 6379,
//    'REDIS_DB'                          => 15,
//    'REDIS_RW_SEPARATE'                 => true,                //Redis读写分离 true 开启
//    'DATA_CACHE_TYPE'                   => 'Redis',             //默认动态缓存为Redis
//    'DATA_CACHE_PREFIX'                 => 'data_',              //缓存前缀
//    'DATA_CACHE_TIME'                   => 3600 * 24 * 7,
//    'SESSION_TYPE'                      => 'Redis',
//    'SESSION_PREFIX'                    => 'session_',
//    'REDIS_TIMEOUT'                     => '300',                //超时时间
//    'REDIS_PERSISTENT'                  => false,                 //是否长连接 false=短连接
//***********************************FTP设置**********************************
    'FTP_CFG' => [
        'host' => '',
        'username' => '',
        'password' => '', //密码
        'port' => '21',
        'timeout' => '90',
    ],
//***********************************梦网短信设置**********************************
    'MW_CFG' => [
        'url' => "",
        'account' => '',
        'password' => '', //密码
    ],
//***********************************艺美短信设置**********************************
    'YM_CFG' => [
        'url' => "",
        'account' => '',
        'password' => '', //密码
        'signature' => '', //密码
    ],
];
