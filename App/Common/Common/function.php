<?php

use Think\Log;

/* * **********************
 * 自定义公共函数
 * fht
 * 2016.04.06
 * ********************* */
define('OP_SUCCESS', 1);
define('OP_FAIL', -1);
define('MAX_ENCRYPT_LENGTH', 117);
define('MAX_DECRYPT_LENGTH', 128);

/**
 * 发送curl请求
 * @static
 * @access public
 * @param $url 请求地址
 * @param $post_data  post方式传输的数据
 * @param $options  curl额外选项，传入curl配置键值对数组
 */
function curl_request($url, $post_data = array(), $options = array()) {
    if (empty($url)) {
        return formatResult(OP_FAIL, "", "url为空!");
    }
    $SSL = substr($url, 0, 8) == "https://" ? true : false;
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if ($SSL) {
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = 2;
    }
    if (!empty($post_data)) {
        // post数据
        curl_setopt($curl, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    }
    foreach ($options as $k => $v) {
        curl_setopt($curl, $k, $v);
    }
    $data = curl_exec($curl);
    $errno = curl_errno($curl);
    if ($errno) {
        $err_msg = curl_error($curl);
        curl_close($curl);
        return formatResult(OP_FAIL, $errno, $err_msg);
    }
    curl_close($curl);
    return formatResult(OP_SUCCESS, $data, "请求成功");
}

//格式化返回值
function formatResult($status, $data = "", $message = "") {
    return array('status' => $status, 'data' => $data, 'message' => $message);
}

//xml解析数组
function xmlToArray($str = "", $options = 0, $class = 'SimpleXMLElement') {
    $xml = simplexml_load_string($str, $class, $options);
    if (empty($xml)) {
        return formatResult(OP_FAIL);
    }
    return formatResult(OP_SUCCESS, json_decode(json_encode($xml), TRUE), "解析成功");
}

//日志记录
function logError($message) {
    Log::record($message, 'ERR');
}

function logDebug($message) {
    Log::record($message, 'DEBUG');
}

function logWarn($message) {
    Log::record($message, 'WARN');
}

//rsa加解密
function rsaEncrypt($data,$public_key) {
    $result = "";
    foreach (str_split($data, MAX_ENCRYPT_LENGTH) as $string) {
        $encryptData = '';
        openssl_public_encrypt($string, $encryptData, $public_key);
        $result .= $encryptData;
    }
    return base64_encode($result);
}

function rsaDecrypt($data,$private_key) {
    $result = "";
    foreach (str_split(base64_decode($data), MAX_DECRYPT_LENGTH) as $string) {
        $decryptData = '';
        openssl_private_decrypt($string, $decryptData, $private_key);
        $result .= $decryptData;
    }
    return $result;
}

/**
 * 将数据(二维数组)转换为csv格式并输出到指定文件，默认输出到标准输出
 * 分界符为逗号,，字段环绕符为双引号"
 * @see fputcsv
 * @param array $data 要转换的数据（二维数组）
 * @param string/resouce $filename 输出目标文件名/或者文件句柄
 */
function output_csv($data, $filename = "php://output", $mode = 'w') {
    if (is_resource($filename)) {
        $outstream = $filename;
    } else {
        $outstream = fopen($filename, $mode);
    }

    array_walk($data, '_output_csv_func', $outstream);

    if (!is_resource($filename)) {
        fclose($outstream);
    }
}

function _output_csv_func(&$vals, $key, $filehandler) {
    array_walk($vals, '_utf8_to_gbk');
    fputcsv($filehandler, $vals, ',', '"');
}

function _utf8_to_gbk(&$vals, $key) {
    $vals = str_replace(',', ' ', $vals);
    $vals = str_replace('"', ' ', $vals);
    $vals = iconv("UTF-8", "GBK", $vals);
}

/**
 * 获取上传的excel
 * @static
 * @access private
 */
function get_upload_excel() {
    $upload = new \Think\Upload();
    $upload->exts = ['xls', 'xlsx'];
    $upload->autoSub = false;
    $info = $upload->upload();
    if (empty($info)) {
        return formatResult(OP_FAIL, [], $upload->getError());
    }
    $excel_path = $upload->rootPath . $info['InputFile']['savename'];
    import('Org.Util.PHPExcel.IOFactory');
    import('Org.Util.PHPExcel');
    //获取reader
    $reader = \PHPExcel_IOFactory::createReader('Excel2007');
    if (!$reader->canRead($excel_path)) {
        $reader = \PHPExcel_IOFactory::createReader('Excel5');
        if (!$reader->canRead($excel_path)) {
            @unlink($excel_path);
            return formatResult(OP_FAIL, ['path' => $excel_path], "读取文件发生异常");
        }
    }
    $objReader = $reader->load($excel_path);
    //获取writer用于报错
    $objWriter = PHPExcel_IOFactory::createWriter($objReader, 'Excel2007');        //excel写入对象
    $objWriter->getPHPExcel()->setActiveSheetIndex(0);
    //设置表头样式
    $styleArray1 = ['font' => ['bold' => true, 'size' => 12, 'color' => [ 'argb' => '00000000']]];
    $objWriter->getPHPExcel()->getActiveSheet()->getStyle('A1:AA1')->applyFromArray($styleArray1);
    $objWriter->getPHPExcel()->getActiveSheet()->getStyle('A1:AA1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objWriter->getPHPExcel()->getActiveSheet()->getStyle('A1:AA1')->getFill()->getStartColor()->setARGB('FFFFFF00');
    return formatResult(OP_SUCCESS, ['path' => $excel_path, 'reader' => $objReader, 'writer' => $objWriter]);
}

//通用ftp方法
function ftpFile($path) {
    $ftp = new Org\Util\FtpUtil(C('PIC_FTP'));
    //if (! $ftp->save($_SERVER['DOCUMENT_ROOT'] . '/' . $path, '/' . $path, true)) {
    if (!$ftp->save($_SERVER['DOCUMENT_ROOT'] . $path, str_replace('/sources', '', $path), true)) {
        //$ftp->getError();
    }
    $ftp->__destruct();
}

//发送梦网短信
function sms_mw($mobile, $content) {
    if (empty($mobile)) {
        return formatResult(OP_FAIL, [], "手机号码不能为空");
    }
    $cfg = C('MW_CFG');
    $post_data = [
        "userId" => $cfg['account'],
        "password" => $cfg['password'],
        "pszMobis" => $mobile,
        "pszMsg" => $content . " ", //加空格，否则某些情况下梦网无法解析
        "iMobiCount" => 1,
        "pszSubPort" => "*",
        "MsgId" => rand(0, 9999999999),
    ];
    $return = curl_request($cfg['url'], http_build_query($post_data), []);
    return xmlToArray($return['data']);
}

//发送艺美短信
function sms_ym($mobile, $content) {
    if (empty($mobile)) {
        return formatResult(OP_FAIL, [], "手机号码不能为空");
    }
    $cfg = C('YM_CFG');
    
    $post_data = [
        'userCode' => $cfg['account'],
        'userPass' => $cfg['password'],
        'DesNo' => $mobile,
        'Msg' => $content.$cfg['signature'],
        'Channel' => "0",
    ];
    $return = curl_request($cfg['url'], http_build_query($post_data), []);
    return xmlToArray($return['data']);
}
/*
  ----------------------------------------------------------------------
  函数:调整图片尺寸或生成缩略图
  返回:TRUE/FALSE
  参数:
  $Image  需要调整的图片(含路径)
  $Dw=450  调整时最大宽度;缩略图时的绝对宽度
  $Dh=450  调整时最大高度;缩略图时的绝对高度
  $Type=1  1,调整尺寸; 2,生成缩略图
  $path='img/';//路径
  $phtypes=array(
  'img/gif',
  'img/jpg',
  'img/jpeg',
  'img/bmp',
  'img/pjpeg',
  'img/x-png'
  );
 */

function Img($Image, $Dw = 450, $Dh = 450, $Type = 1) {
    if (!file_exists($Image)) {
        return FALSE;
    }
    //如果需要生成缩略图,则将原图拷贝一下重新给$Image赋值
    if ($Type != 1) {
        copy($Image, str_replace(".", "_x.", $Image));
        $Image = str_replace(".", "_x.", $Image);
    }
    //取得文件的类型,根据不同的类型建立不同的对象
    $ImgInfo = getimagesize($Image);
    switch ($ImgInfo[2]) {
        case 1:
            $Img = @imagecreatefromgif($Image);
            break;
        case 2:
            $Img = @imagecreatefromjpeg($Image);
            break;
        case 3:
            $Img = @imagecreatefrompng($Image);
            break;
    }
    //如果对象没有创建成功,则说明非图片文件
    if (empty($Img)) {
        //如果是生成缩略图的时候出错,则需要删掉已经复制的文件
        if ($Type != 1) {
            unlink($Image);
        }
        return FALSE;
    }
    //如果是执行调整尺寸操作则
    if ($Type == 1) {
        $w = imagesx($Img);
        $h = imagesy($Img);
        $width = $w;
        $height = $h;
        if ($width > $Dw) {
            $Par = $Dw / $width;
            $width = $Dw;
            $height = $height * $Par;
            if ($height > $Dh) {
                $Par = $Dh / $height;
                $height = $Dh;
                $width = $width * $Par;
            }
        } elseif ($height > $Dh) {
            $Par = $Dh / $height;
            $height = $Dh;
            $width = $width * $Par;
            if ($width > $Dw) {
                $Par = $Dw / $width;
                $width = $Dw;
                $height = $height * $Par;
            }
        } else {
            $width = $width;
            $height = $height;
        }
        $nImg = imagecreatetruecolor($width, $height);   //新建一个真彩色画布
        imagecopyresampled($nImg, $Img, 0, 0, 0, 0, $width, $height, $w, $h); //重采样拷贝部分图像并调整大小
        imagejpeg($nImg, $Image);     //以JPEG格式将图像输出到浏览器或文件
        return TRUE;
        //如果是执行生成缩略图操作则
    } else {
        $w = imagesx($Img);
        $h = imagesy($Img);
        $width = $w;
        $height = $h;
        $nImg = imagecreatetruecolor($Dw, $Dh);
        if ($h / $w > $Dh / $Dw) { //高比较大
            $width = $Dw;
            $height = $h * $Dw / $w;
            $IntNH = $height - $Dh;
            imagecopyresampled($nImg, $Img, 0, -$IntNH / 1.8, 0, 0, $Dw, $height, $w, $h);
        } else {   //宽比较大
            $height = $Dh;
            $width = $w * $Dh / $h;
            $IntNW = $width - $Dw;
            imagecopyresampled($nImg, $Img, -$IntNW / 1.8, 0, 0, 0, $width, $Dh, $w, $h);
        }
        imagejpeg($nImg, $Image);
        return TRUE;
    }
}

function getUUID(){
	$model = M();
	$result = $model->query("SELECT UUID() AS uuid FROM dual");
	return $result[0]['uuid'];
}