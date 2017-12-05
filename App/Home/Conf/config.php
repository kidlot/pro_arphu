<?php

return [
    'URL_ROUTER_ON' => true, // url路由开关
    'URL_ROUTE_RULES' =>array(
        'news/:url'=>'News/detail',
        'project/:url'=>'Project/detail',
        '/^new_tags_(.+)_(\d+)$/'=>'News/tags?id=:1&p=:2',
        '/^new_tags_(.+)$/'=>'News/tags?id=:1',
        '/^([a-zA-Z0-9-]+)$/'=>'List/html?url=:1',
        '/^([a-zA-Z0-9-]+)_(\d+)$/'=>'List/html?url=:1&p=:2',
        '/^Cate\/([a-zA-Z0-9-]+)$/'=>'Cate/html?url=:1',
        'product/:url'=>'Product/html',
    ),
    'TMPL_PARSE_STRING' => [
        '__IMG__' => __ROOT__ . '/Public/Default/home/upload',
        '__FONT__' => __ROOT__ . '/Public/Default/home/font',
        '__CSS__' => __ROOT__ . '/Public/Default/home/css',
        '__JS__' => __ROOT__ . '/Public/Default/home/js',
    ],

    'SESSION_AUTO_START' => false,
];
