<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 默认首页Controller
 * 显示框架基本信息，具体项目请新建模块
 */
class IndexController extends BaseController{

    /**
     * 首页控制器
     */
    public function index()
    {
        $projects = M('Project')->where(['status'=>1])->select();
        $news = M('News')->where(['status'=>1])->select();
        $this->assign('projects',$projects);
        $this->assign('news',$news);
        $this->display();

    }


}
