<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 默认首页Controller
 * 显示框架基本信息，具体项目请新建模块
 */
class ProductController extends BaseController{

    public function html(){
        $url=I('url');
        $list=M('product')->join("arphu_cate ON arphu_cate.id = arphu_product.cate_id")->where(['url'=>$url])->find();
        $this->assign('pro_info',$list);
        $this->display("Product/detail");
    }


}
