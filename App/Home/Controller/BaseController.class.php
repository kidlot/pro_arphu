<?php
// +----------------------------------------------------------------------
// | 多功能后台管理系统-基础控制 除登录不要继承 其他所有控制层必须继承该类
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2020
// +----------------------------------------------------------------------
// | Author: 印建平 <181565561@qq.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {
    public function _initialize(){
        //获取后台分类
        $cates = M('Cate')->select();
        $recent_news = M('News')->where(['status'=>1])->select();
        $this->assign('recent_news',$recent_news);
		$this->assign("cates", $cates);
    }

}