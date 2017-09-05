<?php

namespace Backstage\Controller;
use Backstage\Controller\AdminBaseController;

/**
 * 用户组控制器
 */
class AuthGroupController extends AdminBaseController {

    /**
     * 用户组列表
     */
    public function index() {
        $data = M('AuthGroup')->select();
        $assign = [
            'data' => $data,
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加用户组
     */
    public function add() {
        $data = I('post.');
        unset($data['id']);
        D('AuthGroup')->addData($data);
        $this->success('添加成功', U('AuthGroup/index'));
    }

    /**
     * 修改用户组
     */
    public function edit() {
        $data = I('post.');
        $map = [
            'id' => $data['id'],
        ];
        D('AuthGroup')->editData($map, $data);
        $this->success('修改成功', U('AuthGroup/index'));
    }

    /**
     * 删除用户组
     */
    public function delete() {
        $id = I('get.id');
        $map = [
            'id' => $id,
        ];
        D('AuthGroup')->deleteData($map);
        $this->success('删除成功', U('AuthGroup/index'));
    }

    /**
     * 分配权限
     */
    public function assignAuth() {
        if (IS_POST) {
            $data = I('post.');
            $map = [
                'id' => $data['id'],
            ];
            $data['rules'] = implode(',', $data['rule_ids']);
            D('AuthGroup')->editData($map, $data);
            $this->success('操作成功', U('AuthGroup/index'));
        } else {
            $id = I('get.id');
            // 获取用户组数据
            $group_data = M('AuthGroup')->where(['id' => $id])->find();
            $group_data['rules'] = explode(',', $group_data['rules']);
            // 获取规则数据
            $rule_data = D('AuthRule')->getTreeData('level', 'id', 'title');
            $assign = [
                'group_data' => $group_data,
                'rule_data' => $rule_data
            ];
            $this->assign($assign);
            $this->display();
        }
    }

    /**
     * 成员管理
     */
    public function userManage() {
        $username = trim(I('get.username'));
        $group_id = I('get.group_id');
        $group_name = M('AuthGroup')->getFieldById($group_id, 'title');
        $uids = D('AuthGroupAccess')->getUidsByGroupId($group_id);

        $where = [];
        if ($username !== '') {
            $where = ['username' => $username];
        }
        $user_data = M('AdminUsers')->where($where)->select();

        $assign = [
            'group_id' => $group_id,
            'group_name' => $group_name,
            'uids' => $uids,
            'user_data' => $user_data,
            'username' => $username
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加用户到用户组
     */
    public function addUserToGroup() {
        $data = I('get.');
        $map = [
            'uid' => $data['uid'],
            'group_id' => $data['group_id']
        ];
        $model = D('AuthGroupAccess');
        $count = $model->where($map)->count();
        if ($count == 0) {
            $model->addData($data);
        }
        $this->success('操作成功', U('AuthGroup/userManage', array('group_id' => $data['group_id'], 'username' => $data['username'])));
    }

    /**
     * 将用户移除用户组
     */
    public function removeUserFromGroup() {
        $data = I('get.');
        $map = [
            'uid' => $data['uid'],
            'group_id' => $data['group_id']
        ];
        D('AuthGroupAccess')->deleteData($map);
        $this->success('操作成功', U('AuthGroup/userManage', array('group_id' => $data['group_id'], 'username' => $data['username'])));
    }
}
