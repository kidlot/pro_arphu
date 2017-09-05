<?php

/**
 * 地区控制器
 *
 * @author FHT
 */
namespace Backstage\Controller;
use Backstage\Controller\AdminBaseController;

class RegionController extends AdminBaseController {

    /**
     * 主页
     */
    public function index() {
        $province = M('Region')->where(['pid' => 0])->select();
        $assign = [
            'province' => $province
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 获取省市县联动数据
     */
    public function getRegion() {
        $type = I('post.type');
        $id = I('post.id');
        //type备用
        if (empty($type)) {
            $this->ajaxReturn(['status' => -1, 'message' => "缺少参数！"]);
        }
        //为空说明选择的是 “请选择” ，直接返回空数据以清空下级
        if (empty($id)) {
            $this->ajaxReturn(['status' => 1,'content' => []]);
        }

        $data = M('Region')->where(['pid' => $id])->select();
        $this->ajaxReturn(['status' => 1, 'content' => $data]);
    }
    
     /**
     * 新增地区
     */
    public function add() {
        $pid = I('post.pid');
        $name = I('post.name');
        if(empty($pid)||empty($name)){
            $this->error('参数不全');
        }
        M('Region')->add(['pid'=>$pid,'name'=>$name]);
        $this->success('添加成功', U('Region/index'));
    }
}
