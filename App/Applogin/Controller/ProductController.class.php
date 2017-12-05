<?php

/**
 * 产品控制器
 *
 * @author kid
 */
namespace Applogin\Controller;
use Applogin\Controller\AdminBaseController;

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

    public function add() {
        $data = array(
            'cate_id' => I('edit_cate_id'),
            'product_name' =>I('edit_product_name'),
            'url' =>I('edit_url'),
            'product_pic' =>I('edit_product_pic_url'),
            'general_details' =>I('content4'),
            'technical_info' =>I('content5'),
            'product_range' =>I('content6'),
        );

        M("Product")->add($data);

        $this->success('添加成功', U('Product/index'));
    }

    public function edit() {
        $id = I("id");
        if(empty($id)){
            $this->error('请先选择产品');
        }
        $map = array('id'=>$id);
        $data = array(
            'cate_id' => I('edit_cate_id'),
            'product_name' =>I('edit_product_name'),
            'url' =>I('edit_url'),
            'product_pic' =>I('edit_product_pic_url'),
            'general_details' =>I('content1'),
            'technical_info' =>I('content2'),
            'product_range' =>I('content3'),
        );

        M("Product")->where($map)->save($data);
        $this->success('修改成功', U('Product/index'));
    }

    public function delete() {
        $id = I("id");
        if(empty($id)){
            $this->error('请先选择产品');
        }
        $map = array('id'=>$id);
        M("Product")->where($map)->delete();
        $this->success('删除成功', U('Product/index'));
    }
}
