<?php

/*
 * @author feihaitao
 * @date 2016.06.24
 */
namespace Org\Wechat;
use Think\Log;

class WechatApi {
    /*     * *******************基础接口 start*************************** */

    /**
     * 获取access_token接口
     * 从缓存获取，获取不到时刷新
     * @static
     * @access public
     */
    public static function getAccessToken() {
        $access_token = S('access_token');
        if (empty($access_token['access_token']) || ($access_token['refresh_time'] < time())) {
            $return = self::refreshAccessToken();
            if ($return['status'] != OP_SUCCESS) {
                return $return;
            } else {
                $access_token = $return['data'];
            }
        }
        return formatResult(OP_SUCCESS, $access_token['access_token']);
    }

    /**
     * 刷新access_token接口
     * 一般由定时器调用刷新，特殊情况下由程序调用
     * @static
     * @access public
     */
    public static function refreshAccessToken() {
        $wechat_cfg = C('WECHAT_CFG');
        $url = sprintf("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s", $wechat_cfg['app_id'], $wechat_cfg['secret']);
        $return = curl_request($url);
        if ($return['status'] != OP_SUCCESS) {
            return $return;
        }
        $return = json_decode($return['data'], true);
        if (!empty($return['errcode'])) {
            return formatResult(OP_FAIL, $return, $return['errmsg']);
        }
        //存入缓存
        $return['refresh_time'] = time() + $return['expires_in'] - 600;
        S('access_token', $return, $return['expires_in'] - 1200);
        return formatResult(OP_SUCCESS, $return);
    }

    /**
     * 获取微信服务器ip
     * @static
     * @access public
     */
    public static function getServerIp() {
        $result = self::_doWechatRequest('getcallbackip');
        return $result;
    }
    /*     * *******************基础接口 end*************************** */

    /*     * *******************微信网页授权 start*************************** */

    /**
     * 微信网页授权
     * 需要2.0授权的界面只需要在控制器入口调用该方法即可
     * @static
     * @access public
     * @param $state 自定义参数
     */
    public static function oauthDo($state = 0, $callback = []) {
        // 微信浏览器且未获得授权
        if (self::_isWechatBrowser() && empty(session('openid'))) {
            if (empty(session('redirect_url'))) {
                session('redirect_url', I('server.REQUEST_URI'));
            }
            $wechat_cfg = C('WECHAT_CFG');
            if (!empty(I('get.code'))) {
                $return = self::_getOauthAccessToken($wechat_cfg);
                if ($return['status'] == OP_SUCCESS) {
                    //保存用户的openid和access_token
                    session('openid', $return['data']['openid']);
                    session('access_token', $return['data']['access_token']);
                    session('refresh_token', $return['data']['refresh_token']);
                    if (is_callable($callback)) {
                        call_user_func_array($callback, []);
                    }
                    if (!empty(session('redirect_url'))) {
                        $redirect_url = session('redirect_url');
                        Log::record($redirect_url, 'DEBUG');
                        header('Location:' . $redirect_url, true, 302);
                        exit;
                    }
                } else if ($return['data'] == 6) {//服务器访问不通
                    echo $return['message'];
                    exit;
                } else {
                    self::_oauthJump($wechat_cfg, $state);
                }
            } else {
                self::_oauthJump($wechat_cfg, $state);
            }
        }
    }

    /**
     * 获取网页授权用户的信息
     * @static
     * @access public
     */
    public static function oauthGetUserinfo() {
        if (empty(session('openid')) || empty(session('access_token'))) {
            return formatResult(OP_FAIL, [], "用户未登录！");
        }
        $url = sprintf("https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN ", session('access_token'), session('openid'));
        $return = curl_request($url);
        if ($return['status'] != OP_SUCCESS) {
            return $return;
        }
        $return = json_decode($return['data'], true);
        if (!empty($return['errcode'])) {
            return formatResult(OP_FAIL, $return, $return['errmsg']);
        }
        return formatResult(OP_SUCCESS, $return);
    }

    /**
     * 检验授权凭证（access_token）是否有效
     * @static
     * @access public
     */
    public static function oauthCheckAccessToken() {
        if (empty(session('openid')) || empty(session('refresh_token'))) {
            return formatResult(OP_FAIL, [], "用户未登录！");
        }
        $url = sprintf("https://api.weixin.qq.com/sns/auth?access_token=%s&openid=%s", session('access_token'), session('openid'));
        $return = curl_request($url);
        if ($return['status'] != OP_SUCCESS) {
            return $return;
        }
        $return = json_decode($return['data'], true);
        if (!empty($return['errcode'])) {
            return formatResult(OP_FAIL, $return, $return['errmsg']);
        }
        return formatResult(OP_SUCCESS, $return);
    }

    /**
     * 检验授权凭证（access_token）是否有效
     * @static
     * @access public
     */
    public static function oauthRefreshAccessToken() {
        if (empty(session('openid')) || empty(session('access_token'))) {
            return formatResult(OP_FAIL, [], "用户未登录！");
        }
        $url = sprintf("https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s ", C('WECHAT_CFG.app_id'), session('refresh_token'));
        $return = curl_request($url);
        if ($return['status'] != OP_SUCCESS) {
            return $return;
        }
        $return = json_decode($return['data'], true);
        if (!empty($return['errcode'])) {
            return formatResult(OP_FAIL, $return, $return['errmsg']);
        }
        session('access_token', $return['access_token']);
        session('refresh_token', $return['refresh_token']);
        return formatResult(OP_SUCCESS, $return);
    }
    /*     * *******************微信网页授权 end*************************** */

    /*     * *******************JS SDK接口 start*************************** */

    /**
     * 获取微信js初始化包
     * @static
     * @access public
     */
    public static function getSignPackage() {
        $result = self::getJsApiTicket();
        if ($result['status'] != OP_SUCCESS) {
            return $result;
        }
//        $url = (I('server.HTTPS') == "on") ? "https://" : "http://";
        $url = ((!empty(I('server.HTTPS')) && I('server.HTTPS') !== 'off') || I('server.SERVER_PORT') == 443) ? "https://" : "http://";
        $url .= I('server.HTTP_HOST') . $_SERVER['REQUEST_URI'];
        $timestamp = time();
        $nonceStr = self::_createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket={$result['data']}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";

        $signature = sha1($string);

        $signPackage = [
            "appId" => C('WECHAT_CFG.app_id'),
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        ];
        return formatResult(OP_SUCCESS, $signPackage);
    }

    /**
     * 获取js_api_ticket接口
     * 从缓存获取，获取不到时刷新
     * @static
     * @access public
     */
    public static function getJsApiTicket() {
        $js_api_ticket = S('js_api_ticket');
        if (empty($js_api_ticket['ticket']) || ($js_api_ticket['refresh_time'] < time())) {
            $return = self::refreshJsApiTicket();
            if ($return['status'] != OP_SUCCESS) {
                return $return;
            } else {
                $js_api_ticket = $return['data'];
            }
        }
        return formatResult(OP_SUCCESS, $js_api_ticket['ticket']);
    }

    /**
     * 刷新JsApiTicket接口
     * 一般由定时器调用刷新，特殊情况下由程序调用
     * @static
     * @access public
     */
    public static function refreshJsApiTicket() {
        $return = self::_doWechatRequest('ticket/getticket', ['type' => 'jsapi']);
        if ($return['status'] != OP_SUCCESS) {
            return $return;
        }
        //存入缓存
        $refresh_time = time() + $return['data']['expires_in'] - 600;
        S('js_api_ticket', ['ticket' => $return['data']['ticket'], 'refresh_time' => $refresh_time], $return['data']['expires_in'] - 1200);
        return formatResult(OP_SUCCESS, $return['data']);
    }
    /*     * *******************JS SDK接口 end*************************** */

    /*     * *******************菜单接口 start*************************** */

    /**
     * 查询菜单接口
     * @static
     * @access public
     * @param $menu 菜单数组
     */
    public static function menuCreate($menu = []) {
        if (empty($menu)) {
            return formatResult(OP_FAIL, [], "menu数组不能为空！");
        }
        //中文需要先预处理之后转json，最后urldecode
        $menu = urldecode(json_encode(self::_wxJsonPrepare($menu)));
        $result = self::_doWechatRequest('menu/create', [], $menu);
        return $result;
    }

    /**
     * 查询菜单接口
     * @static
     * @access public
     */
    public static function menuGet() {
        $result = self::_doWechatRequest('menu/get');
        return $result;
    }

    /**
     * 删除菜单接口
     * @static
     * @access public
     */
    public static function menuDelete() {
        $result = self::_doWechatRequest('menu/delete');
        return $result;
    }
    /*     * *******************菜单接口 end*************************** */

    /*     * *******************素材接口 start************************** */

    /**
     * 临时素材上传接口
     * @static
     * @access public
     * @param $type 素材类型，必填
     * @param $file 文件路径
     */
    public static function mediaUpload($type = '', $file = '') {
        if (empty($type) || empty($file)) {
            return formatResult(OP_FAIL, [], "type或file不能为空!");
        }
        if (!is_file($file)) {
            return formatResult(OP_FAIL, [], "文件不存在！");
        }
        $result = self::_doWechatRequest('media/upload', ['type' => $type], ['media' => '@' . $file]);
        return $result;
    }

    /**
     * 临时素材获取接口
     * @static
     * @access public
     * @param $media_id 微信素材id
     * @param $file 文件路径
     */
    public static function mediaGet($media_id = '', $file = '') {
        if (empty($media_id) || empty($file)) {
            return formatResult(OP_FAIL, [], "media_id或file为空!");
        }
        $file_dir = C('WECHAT_CFG.media_path') . 'Media/';
        if (!is_dir($file_dir)) {
            @mkdir($file_dir, 0755, true);
        }
        $fp = fopen($file_dir . $file, 'wb');
        $result = self::_doWechatRequest('media/get', ['media_id' => $media_id], [], false, [CURLOPT_FILE => $fp,]);
        fclose($fp);
        return $result;
    }

    /**
     * 图文素材上传接口
     * @static
     * @access public
     * @param $news articles的内容
     */
    public static function materialAddNews($news = []) {
        if (empty($news)) {
            return formatResult(OP_FAIL, [], "news数组不能为空！");
        }
        $post_data = urldecode(json_encode(self::_wxJsonPrepare(['articles' => $news])));
        $result = self::_doWechatRequest('material/add_news', [], $post_data);
        return $result;
    }

    /**
     * 图文素材修改接口
     * @static
     * @access public
     * @param $media_id 微信素材id
     * @param $news articles的内容
     * @param $index 图文序号
     */
    public static function materialUpdateNews($media_id = '', $news = [], $index = 0) {
        if (empty($news) || empty($media_id)) {
            return formatResult(OP_FAIL, [], "media_id或news数组不能为空！");
        }
        $post_data = [
            'media_id' => $media_id,
            'index' => $index,
            'articles' => $news[$index],
        ];
        $post_data = urldecode(json_encode(self::_wxJsonPrepare($post_data)));
        $result = self::_doWechatRequest('material/update_news', [], $post_data);
        return $result;
    }

    /**
     * 永久素材上传接口
     * @static
     * @access public
     * @param $type 素材类型，必填
     * @param $file 文件路径
     * @param $description video类型的素材需要填写该字段，['title' => 'test', 'introduction' => 'vedio test']
     */
    public static function materialAdd($type = '', $file = '', $description = []) {
        if (empty($type) || empty($file)) {
            return formatResult(OP_FAIL, [], "type或file不能为空!");
        }
        if (!is_file($file)) {
            return formatResult(OP_FAIL, [], "文件不存在！");
        }
        $post_data = ['media' => '@' . $file];
        if ($type == 'video') {
            $post_data['description'] = urldecode(json_encode(self::_wxJsonPrepare($description)));
        }
        $result = self::_doWechatRequest('material/add_material', ['type' => $type], $post_data);
//        if ($result['status'] == OP_SUCCESS) {
//            //保存media_id和图片的微信内部url
//            $model = M('WechatMedia');
//            $data = [
//                'type' => $type,
//                'media_id' => $result['data']['media_id'],
//                'local_path' => APP_PATH . MODULE_NAME . $file,
//            ];
//            if ($type == 'image') {
//                $data['url'] = $result['data']['url'];
//            }
//            $model->data($data)->add();
//        }
        return $result;
    }

    /**
     * 上传图文消息内的图片获取URL 
     * 请注意，本接口所上传的图片不占用公众号的素材库中图片数量的5000个的限制。图片仅支持jpg/png格式，大小必须在1MB以下
     * @static
     * @access public
     * @param $file 文件路径
     */
    public static function materiallUploadimg($file = '') {
        if (!is_file($file)) {
            return formatResult(OP_FAIL, [], "文件不存在！");
        }
        $post_data = ['media' => '@' . $file];
        $result = self::_doWechatRequest('media/uploadimg', [], $post_data);
//        if ($result['status'] == OP_SUCCESS) {
//            //保存图片的微信内部url
//            $model = M('WechatMedia');
//            $data = [
//                'type' => 'uploadimg',
//                'url' => $result['data']['url'],
//                'local_path' => APP_PATH . MODULE_NAME . $file,
//            ];
//            $model->data($data)->add();
//        }
        return $result;
    }

    /**
     * 永久素材获取接口
     * @static
     * @access public
     * @param $type 素材类型，必填
     * @param $media_id 微信素材id
     * @param $file 文件路径
     */
    public static function materialGet($type = '', $media_id = '', $file = '') {
        if (empty($media_id)) {
            return formatResult(OP_FAIL, [], "media_id为空!");
        }
        if (!in_array($type, ['image', 'video', 'voice', 'news'])) {
            return formatResult(OP_FAIL, [], "type类型错误!");
        }
        $post_data = ['media_id' => $media_id];
        if (($type == 'video') || ($type == 'news')) {
            $result = self::_doWechatRequest('material/get_material', [], json_encode($post_data), true, [CURLOPT_TIMEOUT => 0]);
        } else {
            if (empty($file)) {
                return formatResult(OP_FAIL, [], "file为空!");
            }
            $file_dir = C('WECHAT_CFG.media_path') . "Material/{$type}/";
            if (!is_dir($file_dir)) {
                @mkdir($file_dir, 0755, true);
            }
            $fp = fopen($file_dir . $file, 'wb');
            $result = self::_doWechatRequest('material/get_material', [], json_encode($post_data), true, [CURLOPT_FILE => $fp, CURLOPT_TIMEOUT => 0]);
            fclose($fp);
            $result['data'] = '/' . $file_dir . $file;
        }
        return $result;
    }

    /**
     * 永久素材删除接口
     * @static
     * @access public
     * @param $media_id 微信素材id
     */
    public static function materialDel($media_id = '') {
        if (empty($media_id)) {
            return formatResult(OP_FAIL, [], "media_id为空!");
        }
        $result = self::_doWechatRequest('material/del_material', [], json_encode(['media_id' => $media_id]));
//        if (OP_SUCCESS == $result['status']) {
//            $model = M('WechatMedia');
//            $model->where("media_id='{$media_id}'")->delete();
//        }
        return $result;
    }

    /**
     * 获取素材总数接口
     * @static
     * @access public
     */
    public static function materialGetCount() {
        $result = self::_doWechatRequest('material/get_materialcount');
        return $result;
    }

    /**
     * 获取素材列表接口
     * @static
     * @access public
     * @param $type 素材类型，必填
     * @param $offset 偏移
     * @param $count 获取数目
     */
    public static function materialBatchGet($type = '', $offset = 0, $count = 10) {
        if (empty($type)) {
            return formatResult(OP_FAIL, [], "素材类型不能为空！");
        }
        $post_data = [
            'type' => $type,
            'offset' => $offset,
            'count' => $count
        ];
        $result = self::_doWechatRequest('material/batchget_material', [], json_encode($post_data));
        return $result;
    }
    /*     * *******************素材接口 end*************************** */

    /*     * *******************二维码生成接口 start************************** */

    /**
     * 生成二维码
     * @static
     * @access public
     * @param $scene_id 场景id或者场景字符串
     * @param $action_name 类型，默认为场景id的永久二维码
     */
    public static function qrcodeCreate($scene_id = 0, $action_name = 'QR_LIMIT_SCENE') {
        if (empty($scene_id)) {
            return formatResult(OP_FAIL, [], "scene_id不能为空！");
        }
        if ('QR_LIMIT_STR_SCENE' == $action_name) {
            $scene = ['scene' => ['scene_str' => $scene_id]];
        } else {
            $scene = ['scene' => ['scene_id' => $scene_id]];
        }
        $post_data = [
            'action_name' => $action_name,
            'action_info' => $scene,
        ];
        return self::_doWechatRequest('qrcode/create', [], json_encode($post_data));
    }

    /**
     * 用ticket获取二维码
     * @static
     * @access public
     * @param $ticket 
     * @param $show_type 显示类型，默认为直接跳转显示，非0则保存在本地并返回路径
     */
    public static function qrcodeShow($ticket = '', $show_type = 0) {
        if (empty($ticket)) {
            return formatResult(OP_FAIL, [], "ticket不能为空！");
        }
        if ($show_type == 0) {
            header('Location:https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket));
            exit;
        }
        $file_dir = C('WECHAT_CFG.media_path') . "Qrcode/";
        if (!is_dir($file_dir)) {
            @mkdir($file_dir, 0755, true);
        }
        $file_name = $file_dir . md5($ticket) . '.jpg';
        //判断该ticket对应的二维码是否已经保存在本地了
        if (file_exists($file_name)) {
            return formatResult(OP_SUCCESS, ['file' => $file_name]);
        }
        $fp = fopen($file_dir . md5($ticket) . '.jpg', 'wb');
        $result = curl_request('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket), [], [CURLOPT_FILE => $fp, CURLOPT_TIMEOUT => 0]);
        fclose($fp);
        if ($result['status'] != OP_SUCCESS) {
            return $result;
        } else {
            return formatResult(OP_SUCCESS, ['file' => $file_name]);
        }
    }
    /*     * *******************二维码生成接口 end************************** */

    /*     * *******************用户管理接口 start************************** */

    /**
     * 获取用户列表
     * @static
     * @access public
     * @param $next_openid 
     */
    public static function userGetList($next_openid = '') {
        $get_data = [];
        if (!empty($next_openid)) {
            $get_data['next_openid'] = $next_openid;
        }
        $result = self::_doWechatRequest('user/get', $get_data);
        return $result;
    }

    /**
     * 获取用户信息
     * @static
     * @access public
     * @param $open_id 
     * @param $lang 
     */
    public static function userInfo($open_id = '', $lang = 'zh_CN') {
        if (empty($open_id)) {
            return formatResult(OP_FAIL, [], "open_id为空!");
        }
        $get_data = [
            'openid' => $open_id,
            'lang' => $lang,
        ];
        $result = self::_doWechatRequest('user/info', $get_data);
        return $result;
    }

    /**
     * 设置用户备注名
     * @static
     * @access public
     * @param $open_id 
     * @param $remark 备注名
     */
    public static function userUpdateRemark($open_id = '', $remark = '') {
        if (empty($open_id)) {
            return formatResult(OP_FAIL, [], "open_id为空!");
        }
        $post_data = [
            'openid' => $open_id,
            'remark' => $remark,
        ];
        $result = self::_doWechatRequest('user/info/updateremark', [], json_encode($post_data));
        return $result;
    }
    /*     * *******************用户管理接口 end*************************** */

    /*     * *******************模版消息 start*************************** */

    /**
     *  发送模版消息
     * @static
     * @access public
     * @param $touser 目标用户
     * @param $template_id 模版id
     * @param $url 点击跳转的url
     * @param $topcolor 顶部颜色
     * @param $data 模版数据
     */
    public static function sendTemplateMessage($touser = '', $template_id = '', $url = '', $topcolor = '#00FF00', $data = []) {
        if (empty($touser)) {
            return formatResult(OP_FAIL, [], "目标用户为空!");
        }
        if (empty($template_id)) {
            return formatResult(OP_FAIL, [], "模版id为空!");
        }
        $post_data = [
            'touser' => $touser,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $data,
        ];
        $result = self::_doWechatRequest('message/template/send', [], json_encode($post_data));
        return $result;
    }
    /*     * *******************模版消息  end*************************** */

    /*     * *******************以下为私有方法*************************** */

    /**
     * 执行微信接口请求
     * @static
     * @access private
     * @param $type 接口类型
     * @param $get_data  get方式传输的数据
     * @param $post_data  post方式传输的数据
     * @param $ssl 是否https
     * @param $curl_options  curl额外选项
     */
    private static function _doWechatRequest($type = '', $get_data = [], $post_data = [], $ssl = true, $curl_options = []) {
        if (empty($type)) {
            Log::record("接口类型为空！", 'ERR');
            return formatResult(OP_FAIL, [], "接口类型为空!");
        }

        $result = self::getAccessToken();
        if ($result['status'] != OP_SUCCESS) {
            return $result;
        }
        $url = sprintf("api.weixin.qq.com/cgi-bin/%s?access_token=%s", $type, $result['data']);
        foreach ($get_data as $k => $v) {
            $url .= sprintf("&%s=%s", $k, $v);
        }
//        Log::record($url, 'DEBUG');
//        Log::record(print_r($post_data, true), 'DEBUG');

        if ($ssl == true) {
            $url = "https://" . $url;
        } else {
            $url = "http://" . $url;
        }
//        if (!isset($curl_options[CURLOPT_TIMEOUT])) {
//            //默认4秒超时
//            $curl_options[CURLOPT_TIMEOUT] = 4;
//        }
        $return = curl_request($url, $post_data, $curl_options);
        if ($return['status'] != OP_SUCCESS) {
            return $return;
        } else {
            $return = $return['data'];
        }
        Log::record($return, 'DEBUG');
        $return = json_decode($return, true);
        if (isset($return['errcode']) && $return['errcode'] !== 0) {
            Log::record("调用微信接口{$type}错误：{$return['errmsg']}", 'ERR');
            return formatResult(OP_FAIL, $return, $return['errmsg']);
        }
        return formatResult(OP_SUCCESS, $return);
    }

    /**
     * 微信json预处理 ，特殊处理中文
     * @static
     * @access private
     * @param $arr 需要处理的数组
     */
    private static function _wxJsonPrepare($arr = []) {
        foreach ($arr as &$value) {
            if (is_array($value)) {
                //数组递归
                $value = self::_wxJsonPrepare($value);
            } else {
                $value = urlencode($value);
            }
        }
        return $arr;
    }

    /**
     * 随机一个NonceStr
     * @static
     * @access private
     * @param $length 长度
     */
    private static function _createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 检查是否是微信浏览器访问
     * @static
     * @access private
     */
    public static function _isWechatBrowser() {
        if (strpos(I('server.HTTP_USER_AGENT'), 'MicroMessenger') === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 网页2.0跳转
     * @static
     * @access private
     */
    private static function _oauthJump($wechat_cfg, $state) {
        $url = sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect"
            , $wechat_cfg['app_id'], urlencode($wechat_cfg['oauth']['callback']), $wechat_cfg['oauth']['scopes'], $state);
        header('Location:' . $url, true, 302);
        exit;
    }

    /**
     * 获取微信网页授权的access_token
     * @static
     * @access private
     */
    private static function _getOauthAccessToken($wechat_cfg) {
        if (empty(I('get.code'))) {
            return formatResult(OP_FAIL, [], "code为空！");
        }
        $url = sprintf("https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code", $wechat_cfg['app_id'], $wechat_cfg['secret'], I('get.code'));
        $return = curl_request($url);
        if ($return['status'] != OP_SUCCESS) {
            return $return;
        }
        $return = json_decode($return['data'], true);
        if (!empty($return['errcode'])) {
            return formatResult(OP_FAIL, $return, $return['errmsg']);
        }
        return formatResult(OP_SUCCESS, $return);
    }
}
