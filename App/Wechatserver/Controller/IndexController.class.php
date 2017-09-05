<?php

namespace Wechatserver\Controller;
use Think\Controller;
use Think\Log;

/**
 * 微信服务端Controller
 * 接收处理微信推送的消息
 */
class IndexController extends Controller {

    public function index() {
        //鉴权
        if ($this->_checkSignature()) {
            if (empty($_REQUEST["echostr"])) {
                //非url验证消息
                $str = file_get_contents("php://input");
                Log::record($str, 'DEBUG');
                //xml解析
                $result = $this->_getXmlData($str);
                if ($result['status'] != OP_SUCCESS) {
                    Log::record("xml数据读取失败！ \n{$str}", 'ERR');
                    return;
                } else {
                    //微信消息处理
                    $result = $this->handleWechatMessage($result['data']);
                    Log::record(print_r($result, true), 'DEBUG');
                    return;
                }
            } else {
                //url验证消息
                echo $_REQUEST["echostr"];
                return;
            }
        } else {
            Log::record('鉴权失败', 'ERR');
            return;
        }
    }

    /**
     * 微信推送消息处理方法
     */
    protected function handleWechatMessage($message) {
        if (empty($message)) {
            return null;
        }
        switch ($message['MsgType']) {
            case 'text':
                //文本消息
                return $this->handleTextMessage($message);
            case 'image':
                //图片消息
                return $this->handleImageMessage($message);
            case 'voice':
                //语音消息
                return $this->handleVoiceMessage($message);
            case 'video':
                //视频消息
                return $this->handleVideoMessage($message);
            case 'shortvideo':
                //小视频消息
                return $this->handleShortvideoMessage($message);
            case 'location':
                //地理位置消息
                return $this->handleLocationMessage($message);
            case 'link':
                //链接消息
                return $this->handleLinkMessage($message);
            case 'event':
                //事件
                return $this->handleEventMessage($message);
            default :
                Log::record("未知的MsgType：{$message['MsgType']}", 'ERR');
                return null;
        }
    }

    /**
     * 文本消息处理方法，具体处理逻辑在此补充
     */
    protected function handleTextMessage($message) {
        //TBD
    }

    /**
     * 图片消息处理方法，具体处理逻辑在此补充
     */
    protected function handleImageMessage($message) {
        //TBD
    }

    /**
     * 语音消息处理方法，具体处理逻辑在此补充
     */
    protected function handleVoiceMessage($message) {
        //TBD
    }

    /**
     * 视频消息处理方法，具体处理逻辑在此补充
     */
    protected function handleVideoMessage($message) {
        //TBD
    }

    /**
     * 小视频消息处理方法，具体处理逻辑在此补充
     */
    protected function handleShortvideoMessage($message) {
        //TBD
    }

    /**
     * 位置消息处理方法，具体处理逻辑在此补充
     */
    protected function handleLocationMessage($message) {
        //TBD
    }

    /**
     * 链接消息处理方法，具体处理逻辑在此补充
     */
    protected function handleLinkMessage($message) {
        //TBD
    }

    /**
     * 事件消息处理方法，调用对应事件类型的处理方法
     */
    protected function handleEventMessage($message) {
        if (empty($message['Event'])) {
            return null;
        }
        switch ($message['Event']) {
            case 'subscribe':
                //关注
                return $this->handleSubscribeEvent($message);
            case 'unsubscribe':
                //取消关注
                return $this->handleUnsubscribeEvent($message);
            case 'SCAN':
                //扫码
                return $this->handleScanEvent($message);
            case 'LOCATION':
                //上报地理位置的事件
                return $this->handleLocationvent($message);

            /*             * ****************以下为自定义菜单事件*********************** */
            case 'CLICK':
                //点击菜单的事件
                return $this->handleClickEvent($message);
            case 'VIEW':
                //点击菜单跳转链接的事件
                return $this->handleViewEvent($message);
            case 'scancode_push':
                //扫码推事件
                return $this->handleScancodePushEvent($message);
            case 'scancode_waitmsg':
                //扫码推事件且弹出“消息接收中”提示框的事件推送
                return $this->handleScancodeWaitmsgEvent($message);
            case 'pic_sysphoto':
                //弹出系统拍照发图的事件
                return $this->handlePicSysphotoEvent($message);
            case 'pic_photo_or_album':
                //弹出拍照或者相册发图的事件
                return $this->handlePicPhotoOrAlbumEvent($message);
            case 'pic_weixin':
                //弹出微信相册发图器的事件
                return $this->handlePicWeixinEvent($message);
            case 'location_select':
                //弹出地理位置选择器的事件
                return $this->handleLocationSelectEvent($message);
            default :
                Log::record("未知的Event：{$message['Event']}", 'ERR');
                return null;
        }
    }

    /**
     * 关注事件处理方法，具体处理逻辑在此补充
     */
    protected function handleSubscribeEvent($message) {
        //TBD
    }

    /**
     * 取消关注处理方法，具体处理逻辑在此补充
     */
    protected function handleUnsubscribeEvent($message) {
        //TBD
    }

    /**
     * 扫码事件处理方法，具体处理逻辑在此补充
     */
    protected function handleScanEvent($message) {
        //TBD
    }

    /**
     * 上报地理位置的事件处理方法，具体处理逻辑在此补充
     */
    protected function handleLocationvent($message) {
        //TBD
    }

    /**
     * 点击菜单的事件处理方法，具体处理逻辑在此补充
     */
    protected function handleClickEvent($message) {
        //TBD
    }

    /**
     * 点击菜单跳转链接的事件处理方法，具体处理逻辑在此补充
     */
    protected function handleViewEvent($message) {
        //TBD
    }

    /**
     * 扫码推事件处理方法，具体处理逻辑在此补充
     */
    protected function handleScancodePushEvent($message) {
        //TBD
    }

    /**
     * 扫码推事件且弹出“消息接收中”提示框的事件推送处理方法，具体处理逻辑在此补充
     */
    protected function handleScancodeWaitmsgEvent($message) {
        //TBD
    }

    /**
     * 弹出系统拍照发图的事件处理方法，具体处理逻辑在此补充
     */
    protected function handlePicSysphotoEvent($message) {
        //TBD
    }

    /**
     * 弹出拍照或者相册发图的事件处理方法，具体处理逻辑在此补充
     */
    protected function handlePicPhotoOrAlbumEvent($message) {
        //TBD
    }

    /**
     * 弹出微信相册发图器的事件处理方法，具体处理逻辑在此补充
     */
    protected function handlePicWeixinEvent($message) {
        //TBD
    }

    /**
     * 弹出地理位置选择器的事件处理方法，具体处理逻辑在此补充
     */
    protected function handleLocationSelectEvent($message) {
        //TBD
    }
    /*     * ************************以下为私有方法********************************** */

    /**
     * 微信服务器验证
     */
    private function _checkSignature() {
        $signature = $_REQUEST["signature"];
        $timestamp = $_REQUEST["timestamp"];
        $nonce = $_REQUEST["nonce"];
        $token = C('WECHAT_CFG.token');

        $tmp_arr = array($token, $timestamp, $nonce);
        sort($tmp_arr, SORT_STRING);
        $tmp_str = implode($tmp_arr);
        $tmp_str = sha1($tmp_str);

        if ($tmp_str == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * xml解析
     */
    private function _getXmlData($str) {
        $result = xmlToArray($str, LIBXML_NOCDATA);
        if ($result['status'] != OP_SUCCESS) {
            return $result;
        }
        return formatResult(OP_SUCCESS, $result['data'], "解析成功");
    }

    /**
     * 发送回复消息
     * 数组结构需和接口文档一致，字符串类型的自动封装为<![CDATA[{$value}]]>
     */
//   $arr = [
//        'ToUserName' => $message['FromUserName'],
//        'FromUserName' => $message['ToUserName'],
//        'CreateTime' => time(),
//        'MsgType' => 'text',
//        'Content' => $message['Event'],
//    ];
    private function _sendReplyMessage($arr = []) {
        if (empty($arr['MsgType'])) {
            return formatResult(OP_FAIL, '', "回复消息类型或数据为空！");
        }
        $arr = $this->_wxReplyMessagePrepare($arr);
        $xml = data_to_xml(array('xml' => $arr));
        echo $xml;
        return formatResult(OP_SUCCESS, $xml, "回复成功");
    }

    /**
     * 微信消息回复预处理方法
     * @static
     * @access private
     * @param $arr 需要处理的数组
     */
    private function _wxReplyMessagePrepare($arr = []) {
        foreach ($arr as &$value) {
            if (is_array($value)) {
                //数组递归
                $value = $this->_wxReplyMessagePrepare($value);
            } else {
                if (is_string($value)) {
                    $value = "<![CDATA[{$value}]]>";
                }
            }
        }
        return $arr;
    }

    /**
     * 转接客服
     * @static
     * @access private
     * @param $message 微信入口消息
     * @param $KfAccount 指定客服，有需要可传入
     */
    private function _transferCustomerService($message, $KfAccount = '') {
        $arr = [
            'ToUserName' => $message['FromUserName'],
            'FromUserName' => $message['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'transfer_customer_service',
        ];
        if (!empty($KfAccount)) {
            $arr['TransInfo'] = ['KfAccount' => $KfAccount];
        }
        $this->_sendReplyMessage($arr);
    }
}
