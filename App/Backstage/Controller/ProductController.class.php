<?php

/**
 * 产品控制器
 *
 * @author kid
 */
namespace Backstage\Controller;
use Backstage\Controller\AdminBaseController;

class ProductController extends BaseController {

    /**
     * 主页
     */
    public function index() {
        $product = M('Product')->select();
        $assign = array(
            'data' => $product
        );
        $this->assign($assign);
        $this->display();
    }



}
