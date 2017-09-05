<?php

/* * ******************************************************
 *   @author JMQ
 *   @uses $wxApi = new WxApi();
 * ****************************************************** */
namespace Org\Wechat;

class QyhApi {
    private $appId = "wx1137291f7ca71ec4";
    private $appSecret = "rO2jqTFBu44pP_vHkg3iQpPhOFcnnDLGGKifqFI2MGMs_GuSAl8ckLO0jEL3dPCX";
    //const imagePreUrl  ="http://wx.hytncd.heilan.cn/uploads/images/";
    private $imagePreBase = "application/controllers/qyapplication";
    private $imagePreTempUrl = "/uploads/images/tmp/";
    private $imagePreRealUrl = "/uploads/images/";
    public $parameters = array();

    public function __construct($config = array()) {
        if (is_array($config) && !empty($config)) {
            $this->appId = empty($config['appId']) ? $this->appId : $config['appId'];
            $this->appSecret = empty($config['appSecret']) ? $this->appSecret : $config['appSecret'];
            $this->imagePreBase = empty($config['imagePreBase']) ? $this->imagePreBase : $config['imagePreBase'];
            $this->imagePreTempUrl = empty($config['imagePreTempUrl']) ? $this->imagePreTempUrl : $config['imagePreTempUrl'];
            $this->imagePreRealUrl = empty($config['imagePreRealUrl']) ? $this->imagePreRealUrl : $config['imagePreRealUrl'];
        }
    }
    /*     * **************************************************
     * 微信提交API方法，返回微信指定JSON
     * ************************************************** */

    public function wxHttpsRequest($url, $data = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /*     * **************************************************
     * 微信提交API方法2，返回微信指定JSON
     * ************************************************** */

    public function wxHttpsGet($url, $data = null) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * curl请求资源
     * @param  string $url 请求url
     * @return array
     */
    private function wxHttpsRequestHeader($url = '') {
        if ($url == '')
            return;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //这里返回响应报文时，只要body的内容，其他的都不要
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        //获取curl连接句柄的信息
        $httpInfo = curl_getinfo($ch);
        curl_close($ch);

        $info = array_merge(array('body' => $package), array('header' => $httpInfo));

        return $info;
    }

    public function wxSendMsg($data) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$wxAccessToken}";
        $result = $this->wxHttpsRequest($url, $data);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
    /*     * **************************************************
     * 微信获取AccessToken 
     * ************************************************** */

    public function wxAccessToken($appId = NULL, $appSecret = NULL) {
        $appId = is_null($appId) ? $this->appId : $appId;
        $appSecret = is_null($appSecret) ? $this->appSecret : $appSecret;

        $data = json_decode(file_get_contents("application/config/qy_access_token.json"));
        if ($data->expire_time < time()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=" . $appId . "&corpsecret=" . $appSecret;
            $result = $this->wxHttpsRequest($url);
            $jsoninfo = json_decode($result, true);
            $access_token = $jsoninfo["access_token"];
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("application/config/qy_access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }
    /*     * **************************************************
     * 微信获取JsApiTicket
     * ************************************************** */

    public function wxJsApiTicket($appId = NULL, $appSecret = NULL) {
        $appId = is_null($appId) ? $this->appId : $appId;
        $appSecret = is_null($appSecret) ? $this->appSecret : $appSecret;

        $data = json_decode(file_get_contents("application/config/qy_jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=" . $this->wxAccessToken();
            $result = $this->wxHttpsRequest($url);
            $jsoninfo = json_decode($result, true);
            $ticket = $jsoninfo['ticket'];
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("application/config/qy_jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }
    /*     * **************************************************
     * 微信通过OPENID获取用户信息，返回数组
     * ************************************************** */

    public function wxGetUser($openId) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=" . $wxAccessToken . "&userid=" . $openId;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    public function wxCreateUser($data) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token={$wxAccessToken}";
        $result = $this->wxHttpsRequest($url, $data);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    public function wxUpdateUser($data) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token={$wxAccessToken}";
        $result = $this->wxHttpsRequest($url, $data);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }

    public function wxDeleteUser($userid) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token={$wxAccessToken}&userid={$userid}";
        $result = $this->wxHttpsGet($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
    /*     * **************************************************
     * 微信设置OAUTH跳转URL，返回字符串信息 - SCOPE = snsapi_base //验证时不返回确认页面，只能获取OPENID
     * ************************************************** */

    public function wxOauthBase($redirectUrl, $state = "", $appId = NULL) {
        $redirectUrl = urlencode($redirectUrl);
        $appId = is_null($appId) ? $this->appId : $appId;
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . $redirectUrl . "&response_type=code&scope=snsapi_base#wechat_redirect";
        return $url;
    }
    /*     * **************************************************
     * 微信通过oauth得到的code获取用户信息，返回数组
     * ************************************************** */

    public function wxOauthGetUser($code) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$wxAccessToken&code=$code";
        $result = $this->wxHttpsGet($url);
        $jsoninfo = json_decode($result, true);
        if (array_key_exists("errcode", $jsoninfo)) {
            $errcode = $jsoninfo['errcode'];
            switch ($errcode) {
                case '40014':
                    $this->whenTokenError();
                    break;
                default:
                    break;
            }
            return array('UserId' => '');
        }
        return $jsoninfo;
    }

    public function whenTokenError() {
        $data = null;
        $data->expire_time = 0;
        $data->access_token = "";
        $fp = fopen("application/config/qy_access_token.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
    }
    /*     * **************************************************
     * 微信设置OAUTH跳转URL，返回字符串信息 - SCOPE = snsapi_userinfo //获取用户完整信息
     * ************************************************** */

    public function wxOauthUserinfo($redirectUrl, $state = "", $appId = NULL) {
        $redirectUrl = urlencode($redirectUrl);
        $appId = is_null($appId) ? $this->appId : $appId;
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appId . "&redirect_uri=" . $redirectUrl . "&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        return $url;
    }
    /*     * **************************************************
     * 微信OAUTH跳转指定URL
     * ************************************************** */

    public function wxHeader($url) {
        header("location:" . $url);
    }
    /*     * **************************************************
     * 创建自定义菜单
     * ************************************************** */

    public function wxMenuCreate($jsonData, $agentid) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=" . $wxAccessToken . "&agentid=" . $agentid;
        $result = $this->wxHttpsRequest($url, $jsonData);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
    /*     * **************************************************
     * 获取自定义菜单
     * ************************************************** */

    public function wxMenuGet($agentid) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/get?access_token=" . $wxAccessToken . "&agentid=" . $agentid;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
    /*     * **************************************************
     * 删除自定义菜单
     * ************************************************** */

    public function wxMenuDelete($agentid) {
        $wxAccessToken = $this->wxAccessToken();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/delete?access_token=" . $wxAccessToken . "&agentid=" . $agentid;
        $result = $this->wxHttpsRequest($url);
        $jsoninfo = json_decode($result, true);
        return $jsoninfo;
    }
    /*     * ***************************************************
     *   生成随机字符串 - 最长为32位字符串
     * *************************************************** */

    public function wxNonceStr($length = 16, $type = FALSE) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        if ($type == TRUE) {
            return strtoupper(md5(time() . $str));
        } else {
            return $str;
        }
    }
    /*     * *****************************************************
     *   微信格式化数组变成参数格式 - 支持url加密
     * ***************************************************** */

    public function wxSetParam($parameters) {
        if (is_array($parameters) && !empty($parameters)) {
            $this->parameters = $parameters;
            return $this->parameters;
        } else {
            return array();
        }
    }
    /*     * *****************************************************
     *   微信格式化数组变成参数格式 - 支持url加密
     * ***************************************************** */

    public function wxFormatArray($parameters = NULL, $urlencode = FALSE) {
        if (is_null($parameters)) {
            $parameters = $this->parameters;
        }
        $restr = ""; //初始化空
        ksort($parameters); //排序参数
        foreach ($parameters as $k => $v) {//循环定制参数
            if (null != $v && "null" != $v && "sign" != $k) {
                if ($urlencode) {//如果参数需要增加URL加密就增加，不需要则不需要
                    $v = urlencode($v);
                }
                $restr .= $k . "=" . $v . "&"; //返回完整字符串
            }
        }
        if (strlen($restr) > 0) {//如果存在数据则将最后“&”删除
            $restr = substr($restr, 0, strlen($restr) - 1);
        }
        return $restr; //返回字符串
    }
    /*     * *****************************************************
     *   微信MD5签名生成器 - 需要将参数数组转化成为字符串[wxFormatArray方法]
     * ***************************************************** */

    public function wxMd5Sign($content, $privatekey) {
        try {
            if (is_null($privatekey)) {
                throw new Exception("财付通签名key不能为空！");
            }
            if (is_null($content)) {
                throw new Exception("财付通签名内容不能为空");
            }
            $signStr = $content . "&key=" . $privatekey;
            return strtoupper(md5($signStr));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    /*     * *****************************************************
     *   微信Sha1签名生成器 - 需要将参数数组转化成为字符串[wxFormatArray方法]
     * ***************************************************** */

    public function wxSha1Sign($content) {
        try {
            if (is_null($content)) {
                throw new Exception("签名内容不能为空");
            }
            //$signStr = $content;
            return sha1($content);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    /*     * *****************************************************
     *   微信jsApi整合方法 - 通过调用此方法获得jsapi数据
     * ***************************************************** */

    public function wxJsapiPackage() {
        $jsapi_ticket = $this->wxJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

        $timestamp = time();
        $nonceStr = $this->wxNonceStr();

        $signPackage = array(
            "jsapi_ticket" => $jsapi_ticket,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url
        );

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $rawString = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        //$rawString = $this->wxFormatArray($signPackage);
        $signature = $this->wxSha1Sign($rawString);

        $signPackage['signature'] = $signature;
        $signPackage['rawString'] = $rawString;
        $signPackage['appId'] = $this->appId;

        return $signPackage;
    }

    //从微信服务器端下载图片到本地服务器
    public function wxDownImg($media_id) {
        $dbpath = '';
        $path_real = '';
        $filename = '';
        $wxAccessToken = $this->wxAccessToken();
        //调用 多媒体文件下载接口
        $url = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=$wxAccessToken&media_id=$media_id";
        //用curl请求，返回文件资源和curl句柄的信息
        $info = $this->wxHttpsRequestHeader($url);
        //文件类型
        $types = array('image/bmp' => '.bmp', 'image/gif' => '.gif', 'image/jpeg' => '.jpg', 'image/png' => '.png');
        //判断响应首部里的的content-type的值是否是这四种图片类型
        if (isset($types[$info['header']['content_type']])) {
            //文件的uri
            $filename = md5($media_id) . $types[$info['header']['content_type']];
            $dbpath = $this->imagePreTempUrl . $filename;
            $path_real = $this->imagePreBase . $dbpath;
        } else {
            return false;
        }

        //将资源写入文件里
        if ($this->saveFile($path_real, $info['body'])) {
            //将文件保存在图片资源器目录
            $imgPath = $this->imagePreBase . rtrim($this->imagePreRealUrl, '/') . '/img' . date('Ymd');
            $dbImgPath = rtrim($this->imagePreRealUrl, '/') . '/img' . date('Ymd') . '/' . $filename;
            $imgpath_real = self::imagePreBase . $dbImgPath;
            if (!is_dir($imgPath)) {
                if (mkdir($imgPath)) {
                    if (false !== rename($path_real, $imgpath_real)) {
                        return $dbImgPath;
                    }
                }
            } else {
                if (false !== rename($path_real, $imgpath_real)) {
                    return $dbImgPath;
                }
            }
            return $dbpath;
        }
        return false;
    }

    /**
     * 将资源写入文件(本地)
     * @param  string 资源uri
     * @param  source 资源
     * @return boolean
     */
    private function saveFile($path, $fileContent) {
        $fp = fopen($path, 'w');
        if (false !== $fp) {
            if (false !== fwrite($fp, $fileContent)) {
                fclose($fp);
                return true;
            }
        }
        return false;
    }

    /**
     * 将资源写入异域
     * @param  string 资源uri
     * @param  source 资源
     * @return boolean
     */
    private function ftpFile($path, $fileContent) {

        return false;
    }
}
