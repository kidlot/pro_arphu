<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 默认首页Controller
 * 显示框架基本信息，具体项目请新建模块
 */
class CateController extends BaseController{


    public function html(){
        $url=I('url');
        $list=M('product')->alias('p')->join('__CATE__ c ON c.id= p.cate_id')->where(['c.cate_name'=>$url])->select();
        $this->assign('pro_list',$list);
        $this->assign('cate_name',$url);
        $this->display("Product/cate");
    }



}
