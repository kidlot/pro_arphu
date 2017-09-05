<?php

/*
 * @author feihaitao
 * @date 2016.06.24
 */
namespace Org\Wechat;
use Think\Log;

class WechatPay {
	/**
     * 获取支付参数
     * @static
     * @access public
     * @param $rej 统一下单接口返回的参数
     */
	public static function GetPayParameters($rej = []) {
		if(!array_key_exists("appid", $rej)|| !array_key_exists("prepay_id", $rej)|| empty($rej['prepay_id'])){
			return formatResult(OP_FAIL, "", "参数错误");
		}
		$conf = C('WECHAT_CFG.payment');
		$arr["appId"] = $rej["appid"];
		$arr["timeStamp"] = time();
		$arr["nonceStr"] = self::_createNonceStr(32);
		$arr["package"] = "prepay_id=" . $rej['prepay_id'];
		$arr["signType"] = "MD5";
		$arr["paySign"] = self::_makeSign($arr, $conf["KEY"]);
		return formatResult(OP_SUCCESS,$arr);
	}

	/**
     * 统一下单接口
     * @static
     * @access public
     * @param $arr 统一下单接口所需参数
     * @param $timeOut 超时时间
     */
	public static function unifiedOrder($arr = [], $timeOut = 0) {
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		//检测必填参数
		if(!array_key_exists("out_trade_no", $arr) || empty($arr['out_trade_no'])) {
			return formatResult(OP_FAIL, "", "缺少统一支付接口必填参数out_trade_no！");
		}
		else if(!array_key_exists("body", $arr) || empty($arr['body'])) {
			return formatResult(OP_FAIL, "", "缺少统一支付接口必填参数body！");
		}
		else if(!array_key_exists("total_fee", $arr) || empty($arr['total_fee'])) {
			return formatResult(OP_FAIL, "", "缺少统一支付接口必填参数total_fee！");
		}
		else if(!array_key_exists("trade_type", $arr) || empty($arr['trade_type'])) {
			return formatResult(OP_FAIL, "", "缺少统一支付接口必填参数trade_type！");
		}
		
		//关联参数
		if($arr['trade_type'] == "JSAPI" && empty($arr['openid'])) {
			return formatResult(OP_FAIL, "", "统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
		}
		if($arr['trade_type'] == "NATIVE" && empty($arr['product_id'])) {
			return formatResult(OP_FAIL, "", "统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
		}
		
		//异步通知url未设置，则报错返回
		if(!array_key_exists("notify_url", $arr) || empty($arr['notify_url'])) {
			return formatResult(OP_FAIL, "", "缺少统一支付接口必填参数notify_url！");
		}
		$conf = C('WECHAT_CFG');
		$arr["appid"] = $conf["app_id"];
		$arr["mch_id"] = $conf["payment"]["MCHID"];
		$arr["spbill_create_ip"] = I("server.REMOTE_ADDR");
		$arr["nonce_str"] = self::_createNonceStr(32);
		
		//签名
		$arr["sign"] = self::_makeSign($arr, $conf["payment"]["KEY"]);
		$postdata = self::_toXml($arr);
		
		//$startTimeStamp = self::getMillisecond();//请求开始时间
		//self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间，暂时不用
		return self::_sendPayRequest($url,$postdata,$timeOut);
		
	}
	
	/**
     * 支付回调
     * @static
     * @access public
     * @param $length 长度
     */
	public static function notify($callback = []) {
		$str = file_get_contents("php://input");
		$result = xmlToArray($str, LIBXML_NOCDATA);
		if ($result['status'] != OP_SUCCESS) {
			Log::record(print_r($result, true), 'ERR');
            echo "FAIL";
        }
		else{
			Log::record(print_r($result, true), 'DEBUG');
			//验证签名
			$res = self::CheckSign($result['data']);
			if ($res['status'] != OP_SUCCESS) {
				Log::record("签名错误！", 'ERR');
				echo "FAIL";
				exit;
			}
			if(is_callable($callback)){
				call_user_func_array ($callback,[0=>$result['data']]);
			}
			//echo "SUCCESS";
		}
    }
	
	/**
     * 随机一个NonceStr
     * @static
     * @access private
     * @param $length 长度
     */
    private static function _createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
	
	/**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	private static function _makeSign($arr = [], $KEY = "") {
		//签名步骤一：按字典序排序参数
		ksort($arr);
		$string = self::_toUrlParams($arr);
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=" . $KEY;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
	
	/**
	 * 格式化参数格式化成url参数
	 */
	private static function _toUrlParams($arr = []) {
		$buff = "";
		foreach ($arr as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 输出xml字符
	 * @throws WxPayException
	**/
	private static function _toXml($arr = []) {
		if(!is_array($arr) 
			|| count($arr) <= 0)
		{
    		throw new WxPayException("数组数据异常！");
    	}
    	
    	$xml = "<xml>";
    	foreach ($arr as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
	}
	
	/**
	 * 发送curl请求
	 * @throws WxPayException
	**/
	private static function _sendPayRequest($url,$xml,$timeOut){
		$conf = C('WECHAT_CFG');
		$opt = [
			CURLOPT_TIMEOUT => $timeOut,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSLCERTTYPE => 'PEM',
			CURLOPT_SSLCERT => $conf["payment"]["SSLCERT_PATH"],
			CURLOPT_SSLKEYTYPE => 'PEM',
			CURLOPT_SSLKEY => $conf["payment"]["SSLKEY_PATH"],
		];
		
		$response = curl_request($url, $xml, $opt);
		return xmlToArray($response["data"], LIBXML_NOCDATA);
	}
	
	/**
	 * 
	 * 上报数据， 上报的时候将屏蔽所有异常流程
	 * @param string $usrl
	 * @param int $startTimeStamp
	 * @param array $data
	 */
	private static function reportCostTime($url, $startTimeStamp, $data) {
		//如果不需要上报数据
		if(WxPayConfig::REPORT_LEVENL == 0){
			return;
		} 
		//如果仅失败上报
		if(WxPayConfig::REPORT_LEVENL == 1 &&
			 array_key_exists("return_code", $data) &&
			 $data["return_code"] == "SUCCESS" &&
			 array_key_exists("result_code", $data) &&
			 $data["result_code"] == "SUCCESS")
		 {
		 	return;
		 }
		 
		//上报逻辑
		$endTimeStamp = self::getMillisecond();
		$objInput = new WxPayReport();
		$objInput->SetInterface_url($url);
		$objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
		//返回状态码
		if(array_key_exists("return_code", $data)){
			$objInput->SetReturn_code($data["return_code"]);
		}
		//返回信息
		if(array_key_exists("return_msg", $data)){
			$objInput->SetReturn_msg($data["return_msg"]);
		}
		//业务结果
		if(array_key_exists("result_code", $data)){
			$objInput->SetResult_code($data["result_code"]);
		}
		//错误代码
		if(array_key_exists("err_code", $data)){
			$objInput->SetErr_code($data["err_code"]);
		}
		//错误代码描述
		if(array_key_exists("err_code_des", $data)){
			$objInput->SetErr_code_des($data["err_code_des"]);
		}
		//商户订单号
		if(array_key_exists("out_trade_no", $data)){
			$objInput->SetOut_trade_no($data["out_trade_no"]);
		}
		//设备号
		if(array_key_exists("device_info", $data)){
			$objInput->SetDevice_info($data["device_info"]);
		}
		
		try{
			self::report($objInput);
		} catch (WxPayException $e){
			//不做任何处理
		}
	}
	/**
	 * 
	 * 检测签名
	 */
	private static function CheckSign($arr = []) {
		//fix异常
		if(!array_key_exists("sign", $arr)) {
			return formatResult(OP_FAIL, "", "签名错误！");
		}
		$conf = C('WECHAT_CFG');
		$sign = self::_makeSign($arr, $conf["payment"]["KEY"]);
		if($arr["sign"] == $sign){
			return formatResult(OP_SUCCESS);
		}
		return formatResult(OP_FAIL, "", "签名错误！");
	}
	
}