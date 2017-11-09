<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 默认首页Controller
 * 显示框架基本信息，具体项目请新建模块
 */
class ProjectController extends BaseController{


    public function detail(){
        $url=I('url');
        $detail = M('Project')->where(['url'=>$url])->find();
        $this->assign('detail',$detail);
        $this->display();
    }
}
