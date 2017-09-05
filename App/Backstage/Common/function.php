<?php

/**
 *
 * 函数：日志记录
 * @param  string $log   日志内容。
 * @param  string $name （可选）用户名。
 *
 * */
function addAdminLog($log = "", $name = false) {
    $Model = M('AdminLog');
    if (!$name) {
        $user = session('user');
        $data['account'] = $user['username'];
    } else {
        $data['account'] = $name;
    }
    $data['operation_time'] = time();
    $data['ip'] = get_client_ip();
    $data['log'] = $log;
    $Model->data($data)->add();
}

//传递数据以易于阅读的样式格式化后输出
function p($data) {
    // 定义样式
    $str = '<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data = $data ? 'true' : 'false';
    } elseif (is_null($data)) {
        $show_data = 'null';
    } else {
        $show_data = print_r($data, true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}

/**
 * app 图片上传
 * @return string 上传后的图片名
 */
function app_upload_image($path, $maxSize = 52428800) {
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path, '.');
    $path = trim($path, '/');
    $config = array(
        'rootPath' => './', //文件上传保存的根路径
        'savePath' => './' . $path . '/',
        'exts' => array('jpg', 'gif', 'png', 'jpeg', 'bmp'),
        'maxSize' => $maxSize,
        'autoSub' => true,
    );
    $upload = new \Think\Upload($config); // 实例化上传类
    $info = $upload->upload();
    if ($info) {
        foreach ($info as $k => $v) {
            $data[] = trim($v['savepath'], '.') . $v['savename'];
        }
        return $data;
    }
}

/**
 * app 视频上传
 * @return string 上传后的视频名
 */
function app_upload_video($path, $maxSize = 52428800) {
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path, '.');
    $path = trim($path, '/');
    $config = array(
        'rootPath' => './', //文件上传保存的根路径
        'savePath' => './' . $path . '/',
        'exts' => array('mp4', 'avi', '3gp', 'rmvb', 'gif', 'wmv', 'mkv', 'mpg', 'vob', 'mov', 'flv', 'swf', 'mp3', 'ape', 'wma', 'aac', 'mmf', 'amr', 'm4a', 'm4r', 'ogg', 'wav', 'wavpack'),
        'maxSize' => $maxSize,
        'autoSub' => true,
    );
    $upload = new \Think\Upload($config); // 实例化上传类
    $info = $upload->upload();
    if ($info) {
        foreach ($info as $k => $v) {
            $data[] = trim($v['savepath'], '.') . $v['savename'];
        }
        return $data;
    }
}

/**
 * 返回文件格式
 * @param  string $str 文件名
 * @return string      文件格式
 */
function file_format($str) {
    // 取文件后缀名
    $str = strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $image = array('webp', 'jpg', 'png', 'ico', 'bmp', 'gif', 'tif', 'pcx', 'tga', 'bmp', 'pxc', 'tiff', 'jpeg', 'exif', 'fpx', 'svg', 'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'hdri');
    // 视频格式
    $video = array('mp4', 'avi', '3gp', 'rmvb', 'gif', 'wmv', 'mkv', 'mpg', 'vob', 'mov', 'flv', 'swf', 'mp3', 'ape', 'wma', 'aac', 'mmf', 'amr', 'm4a', 'm4r', 'ogg', 'wav', 'wavpack');
    // 压缩格式
    $zip = array('rar', 'zip', 'tar', 'cab', 'uue', 'jar', 'iso', 'z', '7-zip', 'ace', 'lzh', 'arj', 'gzip', 'bz2', 'tz');
    // 文档格式
    $text = array('exe', 'doc', 'ppt', 'xls', 'wps', 'txt', 'lrc', 'wfs', 'torrent', 'html', 'htm', 'java', 'js', 'css', 'less', 'php', 'pdf', 'pps', 'host', 'box', 'docx', 'word', 'perfect', 'dot', 'dsf', 'efe', 'ini', 'json', 'lnk', 'log', 'msi', 'ost', 'pcs', 'tmp', 'xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $image):
            return 'image';
        case in_array($str, $video):
            return 'video';
        case in_array($str, $zip):
            return 'zip';
        case in_array($str, $text):
            return 'text';
        default:
            return 'unknow';
    }
}

/**
 * 返回用户id
 * @return integer 用户id
 */
function get_uid() {
    return session("user.id");
}

/**
 * 返回iso、Android、ajax的json格式数据
 * @param  array  $data           需要发送到前端的数据
 * @param  string  $error_message 成功或者错误的提示语
 * @param  integer $error_code    状态码： 0：成功  1：失败
 * @return string                 json格式的数据
 */
function ajax_return($data = '', $error_message = '成功', $error_code = 1) {
    $all_data = array(
        'error_code' => $error_code,
        'error_message' => $error_message,
    );
    if ($data !== '') {
        $all_data['data'] = $data;
        // app 禁止使用和为了统一字段做的判断
        $reserved_words = array('id', 'title', 'price', 'product_title', 'product_id', 'product_category', 'product_number');
        foreach ($reserved_words as $k => $v) {
            if (array_key_exists($v, $data)) {
                echo 'app不允许使用【' . $v . '】这个键名 —— 此提示是function.php 中的ajax_return函数返回的';
                die;
            }
        }
    }
    // 如果是ajax或者app访问；则返回json数据 pc访问直接p出来
    echo json_encode($all_data);
    exit(0);
}

/**
 * 检测是否登录
 * @return boolean 是否登录
 */
function check_login() {
    if (!empty(get_uid())) {
        return true;
    } else {
        return false;
    }
}

/**
 * 删除指定的标签和内容
 * @param array $tags 需要删除的标签数组
 * @param string $str 数据源
 * @param string $content 是否删除标签内的内容 0保留内容 1不保留内容
 * @return string
 */
function strip_html_tags($tags, $str, $content = 0) {
    if ($content) {
        $html = array();
        foreach ($tags as $tag) {
            $html[] = '/(<' . $tag . '.*?>[\s|\S]*?<\/' . $tag . '>)/';
        }
        $data = preg_replace($html, '', $str);
    } else {
        $html = array();
        foreach ($tags as $tag) {
            $html[] = "/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/i";
        }
        $data = preg_replace($html, '', $str);
    }
    return $data;
}

/**
 * 传递ueditor生成的内容获取其中图片的路径
 * @param  string $str 含有图片链接的字符串
 * @return array       匹配的图片数组
 */
function get_ueditor_image_path($str) {
    $preg = '/\/Upload\/image\/u(m)?editor\/\d*\/\d*\.[jpg|jpeg|png|bmp]*/i';
    preg_match_all($preg, $str, $data);
    return current($data);
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $suffix 截断显示字符
 * @param string $charset 编码格式
 * @return string
 */
function re_substr($str, $start = 0, $length, $suffix = true, $charset = "utf-8") {
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    $omit = mb_strlen($str) >= $length ? '...' : '';
    return $suffix ? $slice . $omit : $slice;
}

// 设置验证码
function show_verify($config = '') {
    if ($config == '') {
        $config = array(
            'codeSet' => '1234567890',
            'fontSize' => 30,
            'useCurve' => false,
            'imageH' => 60,
            'imageW' => 240,
            'length' => 4,
            'fontttf' => '4.ttf',
        );
    }
    $verify = new \Think\Verify($config);
    return $verify->entry();
}

// 检测验证码
function check_verify($code) {
    $verify = new \Think\Verify();
    return $verify->check($code);
}

/**
 * 取得根域名
 * @param type $domain 域名
 * @return string 返回根域名
 */
function get_url_to_domain($domain) {
    $re_domain = '';
    $domain_postfix_cn_array = array("com", "net", "org", "gov", "edu", "com.cn", "cn");
    $array_domain = explode(".", $domain);
    $array_num = count($array_domain) - 1;
    if ($array_domain[$array_num] == 'cn') {
        if (in_array($array_domain[$array_num - 1], $domain_postfix_cn_array)) {
            $re_domain = $array_domain[$array_num - 2] . "." . $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        } else {
            $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
        }
    } else {
        $re_domain = $array_domain[$array_num - 1] . "." . $array_domain[$array_num];
    }
    return $re_domain;
}

/**
 * 按符号截取字符串的指定部分
 * @param string $str 需要截取的字符串
 * @param string $sign 需要截取的符号
 * @param int $number 如是正数以0为起点从左向右截  负数则从右向左截
 * @return string 返回截取的内容
 */
/*  示例
  $str='123/456/789';
  cut_str($str,'/',0);  返回 123
  cut_str($str,'/',-1);  返回 789
  cut_str($str,'/',-2);  返回 456
  具体参考 http://www.baijunyao.com/index.php/Home/Index/article/aid/18
 */
function cut_str($str, $sign, $number) {
    $array = explode($sign, $str);
    $length = count($array);
    if ($number < 0) {
        $new_array = array_reverse($array);
        $abs_number = abs($number);
        if ($abs_number > $length) {
            return 'error';
        } else {
            return $new_array[$abs_number - 1];
        }
    } else {
        if ($number >= $length) {
            return 'error';
        } else {
            return $array[$number];
        }
    }
}

/**
 * 获取一定范围内的随机数字
 * 跟rand()函数的区别是 位数不足补零 例如
 * rand(1,9999)可能会得到 465
 * rand_number(1,9999)可能会得到 0465  保证是4位的
 * @param integer $min 最小值
 * @param integer $max 最大值
 * @return string
 */
function rand_number($min = 1, $max = 9999) {
    return sprintf("%0" . strlen($max) . "d", mt_rand($min, $max));
}

/**
 * 生成一定数量的随机数，并且不重复
 * @param integer $number 数量
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @return string
 */
function build_count_rand($number, $length = 4, $mode = 1) {
    if ($mode == 1 && $length < strlen($number)) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand = array();
    for ($i = 0; $i < $number; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $unqiue = array_unique($rand);
    if (count($unqiue) == count($rand)) {
        return $rand;
    }
    $count = count($rand) - count($unqiue);
    for ($i = 0; $i < $count * 3; $i++) {
        $rand[] = rand_string($length, $mode);
    }
    $rand = array_slice(array_unique($rand), 0, $number);
    return $rand;
}

/**
 * 生成不重复的随机数
 * @param  int $start  需要生成的数字开始范围
 * @param  int $end 结束范围
 * @param  int $length 需要生成的随机数个数
 * @return array       生成的随机数
 */
function get_rand_number($start = 1, $end = 10, $length = 4) {
    $connt = 0;
    $temp = array();
    while ($connt < $length) {
        $temp[] = rand($start, $end);
        $data = array_unique($temp);
        $connt = count($data);
    }
    sort($data);
    return $data;
}

/**
 * 实例化page类
 * @param  integer  $count 总数
 * @param  integer  $limit 每页数量
 * @return subject       page类
 */
function new_page($count, $limit = 10) {
    return new \Org\Nx\Page($count, $limit);
}

/**
 * 获取分页数据
 * @param  subject  $model  model对象
 * @param  array    $map    where条件
 * @param  string   $order  排序规则
 * @param  integer  $limit  每页数量
 * @return array            分页数据
 */
function get_page_data($model, $map, $order = '', $limit = 10) {
    $count = $model
        ->where($map)
        ->count();
    $page = new_page($count, $limit);
    // 获取分页数据
    $list = $model
        ->where($map)
        ->order($order)
        ->limit($page->firstRow . ',' . $page->listRows)
        ->select();
    $data = array(
        'data' => $list,
        'page' => $page->show()
    );
    return $data;
}

// *
// * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
// * @param  string   $format  文件格式限制
// * @param  integer  $maxSize 允许的上传文件最大值 52428800
// * @return booler            返回ajax的json格式数据

function post_upload($path = 'file', $format = 'empty', $maxSize = '52428800') {
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path, '/');
    // 添加Upload根目录
    $path = strtolower(substr($path, 0, 6)) === 'upload' ? ucfirst($path) : 'Upload/' . $path;
    // 上传文件类型控制
    $ext_arr = array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'pdf')
    );
    if (!empty($_FILES)) {
        // 上传文件配置
        $config = array(
            'maxSize' => $maxSize, //   上传文件最大为50M
            'rootPath' => './', //文件上传保存的根路径
            'savePath' => './' . $path . '/', //文件上传的保存路径（相对于根路径）
            'saveName' => array('uniqid', ''), //上传文件的保存规则，支持数组和字符串方式定义
            'autoSub' => true, //  自动使用子目录保存上传文件 默认为true
            'exts' => isset($ext_arr[$format]) ? $ext_arr[$format] : '',
        );
        // 实例化上传
        $upload = new \Think\Upload($config);
        // 调用上传方法
        $info = $upload->upload();
        $data = array();
        if (!$info) {
            // 返回错误信息
            $error = $upload->getError();
            $data['error_info'] = $error;
            return $data;
        } else {
            // 返回成功信息
            foreach ($info as $file) {
                $data['name'] = trim($file['savepath'] . $file['savename'], '.');
                return $data;
            }
        }
    }
}

/**
 * 上传文件类型控制   此方法仅限ajax上传使用
 * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string   $format  文件格式限制
 * @param  integer  $maxSize 允许的上传文件最大值 52428800
 * @return booler       返回ajax的json格式数据
 */
function upload($path = 'file', $format = 'empty', $maxSize = '52428800') {
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path = trim($path, '/');
    // 添加Upload根目录
    $path = strtolower(substr($path, 0, 6)) === 'upload' ? ucfirst($path) : 'Upload/' . $path;
    // 上传文件类型控制
    $ext_arr = array(
        'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
        'photo' => array('jpg', 'jpeg', 'png'),
        'flash' => array('swf', 'flv'),
        'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
        'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2', 'pdf')
    );
    if (!empty($_FILES)) {
        // 上传文件配置
        $config = array(
            'maxSize' => $maxSize, //   上传文件最大为50M
            'rootPath' => './', //文件上传保存的根路径
            'savePath' => './' . $path . '/', //文件上传的保存路径（相对于根路径）
            'saveName' => array('uniqid', ''), //上传文件的保存规则，支持数组和字符串方式定义
            'autoSub' => true, //  自动使用子目录保存上传文件 默认为true
            'exts' => isset($ext_arr[$format]) ? $ext_arr[$format] : '',
        );
        // 实例化上传
        $upload = new \Think\Upload($config);
        // 调用上传方法
        $info = $upload->upload();
        $data = array();
        if (!$info) {
            // 返回错误信息
            $error = $upload->getError();
            $data['error_info'] = $error;
            echo json_encode($data);
        } else {
            // 返回成功信息
            foreach ($info as $file) {
                $data['name'] = trim($file['savepath'] . $file['savename'], '.');
                echo json_encode($data);
            }
        }
    }
}

/**
 * 使用curl获取远程数据
 * @param  string $url url连接
 * @return string      获取到的数据
 */
function curl_get_contents($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
    //curl_setopt($ch,CURLOPT_HEADER,1);                //是否显示头部信息
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);   //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER, _REFERER_);        //设置 referer
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);          //跟踪301
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}
/*
 * 计算星座的函数 string get_zodiac_sign(string month, string day)
 * 输入：月份，日期
 * 输出：星座名称或者错误信息
 */

function get_zodiac_sign($month, $day) {
    // 检查参数有效性
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
        return (false);
    // 星座名称以及开始日期
    $signs = array(
        array("20" => "水瓶座"),
        array("19" => "双鱼座"),
        array("21" => "白羊座"),
        array("20" => "金牛座"),
        array("21" => "双子座"),
        array("22" => "巨蟹座"),
        array("23" => "狮子座"),
        array("23" => "处女座"),
        array("23" => "天秤座"),
        array("24" => "天蝎座"),
        array("22" => "射手座"),
        array("22" => "摩羯座")
    );
    list($sign_start, $sign_name) = each($signs[(int) $month - 1]);
    if ($day < $sign_start)
        list($sign_start, $sign_name) = each($signs[($month - 2 < 0) ? $month = 11 : $month -= 2]);
    return $sign_name;
}

/**
 * 将路径转换加密
 * @param  string $file_path 路径
 * @return string            转换后的路径
 */
function path_encode($file_path) {
    return rawurlencode(base64_encode($file_path));
}

/**
 * 将路径解密
 * @param  string $file_path 加密后的字符串
 * @return string            解密后的路径
 */
function path_decode($file_path) {
    return base64_decode(rawurldecode($file_path));
}

/**
 * 根据文件后缀的不同返回不同的结果
 * @param  string $str 需要判断的文件名或者文件的id
 * @return integer     1:图片  2：视频  3：压缩文件  4：文档  5：其他
 */
function file_category($str) {
    // 取文件后缀名
    $str = strtolower(pathinfo($str, PATHINFO_EXTENSION));
    // 图片格式
    $images = array('webp', 'jpg', 'png', 'ico', 'bmp', 'gif', 'tif', 'pcx', 'tga', 'bmp', 'pxc', 'tiff', 'jpeg', 'exif', 'fpx', 'svg', 'psd', 'cdr', 'pcd', 'dxf', 'ufo', 'eps', 'ai', 'hdri');
    // 视频格式
    $video = array('mp4', 'avi', '3gp', 'rmvb', 'gif', 'wmv', 'mkv', 'mpg', 'vob', 'mov', 'flv', 'swf', 'mp3', 'ape', 'wma', 'aac', 'mmf', 'amr', 'm4a', 'm4r', 'ogg', 'wav', 'wavpack');
    // 压缩格式
    $zip = array('rar', 'zip', 'tar', 'cab', 'uue', 'jar', 'iso', 'z', '7-zip', 'ace', 'lzh', 'arj', 'gzip', 'bz2', 'tz');
    // 文档格式
    $document = array('exe', 'doc', 'ppt', 'xls', 'wps', 'txt', 'lrc', 'wfs', 'torrent', 'html', 'htm', 'java', 'js', 'css', 'less', 'php', 'pdf', 'pps', 'host', 'box', 'docx', 'word', 'perfect', 'dot', 'dsf', 'efe', 'ini', 'json', 'lnk', 'log', 'msi', 'ost', 'pcs', 'tmp', 'xlsb');
    // 匹配不同的结果
    switch ($str) {
        case in_array($str, $images):
            return 1;
            break;
        case in_array($str, $video):
            return 2;
            break;
        case in_array($str, $zip):
            return 3;
            break;
        case in_array($str, $document):
            return 4;
            break;
        default:
            return 5;
            break;
    }
}

/**
 * 组合缩略图
 * @param  string  $file_path  原图path
 * @param  integer $size       比例
 * @return string              缩略图
 */
function get_min_image_path($file_path, $width = 170, $height = 170) {
    $min_path = str_replace('.', '_' . $width . '_' . $height . '.', trim($file_path, '.'));
    $min_path = 'http://xueba17.oss-cn-beijing.aliyuncs.com' . $min_path;
    return $min_path;
}

/**
 * 不区分大小写的in_array()
 * @param  string $str   检测的字符
 * @param  array  $array 数组
 * @return boolear       是否in_array
 */
function in_iarray($str, $array) {
    $str = strtolower($str);
    $array = array_map('strtolower', $array);
    if (in_array($str, $array)) {
        return true;
    }
    return false;
}

/**
 * 传入时间戳,计算距离现在的时间
 * @param  number $time 时间戳
 * @return string       返回多少以前
 */
function word_time($time) {
    $time = (int) substr($time, 0, 10);
    $int = time() - $time;
    $str = '';
    if ($int <= 2) {
        $str = sprintf('刚刚', $int);
    } elseif ($int < 60) {
        $str = sprintf('%d秒前', $int);
    } elseif ($int < 3600) {
        $str = sprintf('%d分钟前', floor($int / 60));
    } elseif ($int < 86400) {
        $str = sprintf('%d小时前', floor($int / 3600));
    } else {
        $str = date('Y-m-d H:i:s', $time);
    }
    return $str;
}

/**
 * 生成缩略图
 * @param  string  $image_path 原图path
 * @param  integer $width      缩略图的宽
 * @param  integer $height     缩略图的高
 * @return string             缩略图path
 */
function crop_image($image_path, $width = 170, $height = 170) {
    $image_path = trim($image_path, '.');
    $min_path = '.' . str_replace('.', '_' . $width . '_' . $height . '.', $image_path);
    $image = new \Think\Image();
    $image->open('http://xueba17.oss-cn-beijing.aliyuncs.com' . $image_path);
    // 生成一个居中裁剪为$width*$height的缩略图并保存
    $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_CENTER)->save($min_path);
    oss_upload($min_path);
    return $min_path;
}

/**
 * 检测webuploader上传是否成功
 * @param  string $file_path post中的字段
 * @return boolear           是否成功
 */
function upload_success($file_path) {
    // 为兼容传进来的有数组；先转成json
    $file_path = json_encode($file_path);
    // 如果有undefined说明上传失败
    if (strpos($file_path, 'undefined') !== false) {
        return false;
    }
    // 如果没有.符号说明上传失败
    if (strpos($file_path, '.') === false) {
        return false;
    }
    // 否则上传成功则返回true
    return true;
}

/**
 * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
 */
function emoji_encode($str) {
    if (!is_string($str))
        return $str;
    if (!$str || $str == 'undefined')
        return '';

    $text = json_encode($str); //暴露出unicode
    $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function($str) {
        return addslashes($str[0]);
    }, $text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
    return json_decode($text);
}

/**
 * 检测是否是手机访问
 */
function is_mobile() {
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';

    function _is_mobile($substrs, $text) {
        foreach ($substrs as $substr)
            if (false !== strpos($text, $substr)) {
                return true;
            }
        return false;
    }
    $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

    $found_mobile = _is_mobile($mobile_os_list, $useragent_commentsblock) ||
        _is_mobile($mobile_token_list, $useragent);
    if ($found_mobile) {
        return true;
    } else {
        return false;
    }
}

/**
 * 将utf-16的emoji表情转为utf8文字形
 * @param  string $str 需要转的字符串
 * @return string      转完成后的字符串
 */
function escape_sequence_decode($str) {
    $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';
    return preg_replace_callback($regex, function($matches) {
        if (isset($matches[3])) {
            $cp = hexdec($matches[3]);
        } else {
            $lead = hexdec($matches[1]);
            $trail = hexdec($matches[2]);
            $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
        }

        if ($cp > 0xD7FF && 0xE000 > $cp) {
            $cp = 0xFFFD;
        }
        if ($cp < 0x80) {
            return chr($cp);
        } else if ($cp < 0xA0) {
            return chr(0xC0 | $cp >> 6) . chr(0x80 | $cp & 0x3F);
        }
        $result = html_entity_decode('&#' . $cp . ';');
        return $result;
    }, $str);
}

/**
 * 获取当前访问的设备类型
 * @return integer 1：其他  2：iOS  3：Android
 */
function get_device_type() {
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 1;
    //分别进行判断
    if (strpos($agent, 'iphone') !== false || strpos($agent, 'ipad') !== false) {
        $type = 2;
    }
    if (strpos($agent, 'android') !== false) {
        $type = 3;
    }
    return $type;
}

/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) {//位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str.= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}
