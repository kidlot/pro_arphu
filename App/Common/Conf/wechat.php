<?php

return [
    'WECHAT_CFG' => [
        /**
         * 账号基本信息，请从微信公众平台/开放平台获取
         */
        'title' => '',
        'token' => '',
        'aes_key' => '',
        'app_id' => '',
        'secret' => '',
        /**
         * 本地素材保存地址
         */
        'media_path' => 'Public/Wechat/',
        /**
         * OAuth 配置
         *
         * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
         * callback：OAuth授权完成后的回调页地址
         */
        'oauth' => [
            'scopes' => 'snsapi_userinfo',
            'callback' => 'http://htfei.com/Wechat/index',
        ],
        /**
         * TODO: 修改这里配置为您自己申请的商户信息  微信支付
         * MCHID：商户号（必须配置，开户邮件中可查看）
         * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
         * SSLCERT_PATH,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
         * KSSLKEY_PATH：API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
         */
        'payment' => [
            'MCHID' => '',
            'KEY' => '',
            'SSLCERT_PATH' => '',
            'SSLKEY_PATH' => '',
            'REPORT_LEVENL' => 1,               //上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
        ],
    ],
];

