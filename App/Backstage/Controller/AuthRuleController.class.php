<?php

namespace Backstage\Controller;
use Backstage\Controller\AdminBaseController;

/**
 * 权限控制器
 */
class AuthRuleController extends AdminBaseController {

    /**
     * 权限列表
     */
    public function index() {
        $data = D('AuthRule')->getTreeData('tree', 'id', 'title');
        $assign = array(
            'data' => $data
        );
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加权限
     */
    public function add() {
        $data = I('post.');
        unset($data['id']);
        if (empty($data['title']) || empty($data['name'])) {
            $this->error('参数不全');
        }
        $model = D('AuthRule');
        if (intval($model->where(['name' => $data['name']])->getField('id')) > 0) {
            $this->error('该权限已存在');
        }
        $model->addData($data);
        $this->success('添加成功', U('AuthRule/index'));
    }

    /**
     * 修改权限
     */
    public function edit() {
        $data = I('post.');
        $map = array(
            'id' => $data['id']
        );
        if (empty($data['title']) || empty($data['name'])) {
            $this->error('参数不全');
        }
        $model = D('AuthRule');
        $exist_id = intval($model->where(['name' => $data['name']])->getField('id'));
        if (($exist_id > 0) && ($exist_id != $data['id'])) {
            $this->error('该权限已存在');
        }
        $model->editData($map, $data);
        $this->success('修改成功', U('AuthRule/index'));
    }

    /**
     * 删除权限
     */
    public function delete() {
        $id = I('get.id');
        $map = array(
            'id' => $id
        );
        $result = D('AuthRule')->deleteData($map);
        if ($result) {
            $this->success('删除成功', U('AuthRule/index'));
        } else {
            $this->error('请先删除子权限');
        }
    }
}
