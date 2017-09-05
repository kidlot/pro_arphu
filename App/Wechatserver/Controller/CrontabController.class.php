<?php

namespace Wechatserver\Controller;
use Think\Controller;
use Org\Wechat\WechatApi;
use Think\Log;

/**
 * 定时器
 */
class CrontabController extends Controller {

    public function index() {
        WechatApi::refreshAccessToken();
        WechatApi::refreshJsApiTicket();
//        Log::record(print_r(S('access_token'),true),'ERR');
//        Log::record(print_r(S('js_api_ticket'),true),'ERR');
    }
}
