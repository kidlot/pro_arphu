<?php

namespace Applogin\Controller;
use Applogin\Controller\AdminBaseController;

/**
 * 管理员控制器
 */
class AdminUserController extends AdminBaseController {

    /**
     * 管理员列表
     */
    public function index() {
        $data = D('AuthGroupAccess')->getAllData();
        $assign = [
            'data' => $data
        ];
        $this->assign($assign);
        $this->display();
    }

    /**
     * 添加管理员
     */
    public function add() {

        if (IS_POST) {
            $data = I('post.');

            $model = D('AdminUsers');
            $exist = $model->where(['username' => $data['username']])->count();
            if ($exist > 0) {
                $this->error("该用户名已存在");
            }
            $password = I('post.password');
            if ($password) {
                $data['salt'] = rand_string(15);
                $md5Password = md5($password . $data['salt']);
                $data['password'] = $md5Password;
            }
            $result = $model->addData($data);
            if ($result) {
                if (!empty($data['group_ids'])) {
                    foreach ($data['group_ids'] as $v) {
                        $group = [
                            'uid' => $result,
                            'group_id' => $v
                        ];
                        D('AuthGroupAccess')->addData($group);
                    }
                }
                // 操作成功
                $this->success('添加成功', U('AdminUser/index'));
            } else {
                $error_word = $model->getError();
                // 操作失败
                $this->error($error_word);
            }
        } else {
            $data = D('AuthGroup')->select();
            $assign = [
                'data' => $data
            ];
            $this->assign($assign);
            $this->display();
        }
    }

    /**
     * 修改管理员
     */
    public function edit() {
        if (IS_POST) {
            $data = I('post.');
            // 组合where数组条件
            $uid = $data['id'];
            $map = [
                'id' => $uid
            ];
            // 修改商品
            D('AuthGroupAccess')->deleteData(['uid' => $uid]);
            foreach ($data['group_ids'] as $v) {
                $group = [
                    'uid' => $uid,
                    'group_id' => $v
                ];
                D('AuthGroupAccess')->addData($group);
            }
            // 如果修改密码则md5
            if (!empty($data['password'])) {
                $data['salt'] = rand_string(15);
                $md5Password = md5($data['password'] . $data['salt']);
                $data['password'] = $md5Password;
            }else{
                unset($data['password']);
            }

            $result = D('AdminUsers')->editData($map, $data);
            if ($result) {
                // 操作成功
                $this->success('编辑成功', U('AdminUser/edit', ['id' => $uid]));
            } else {
                $error_word = D('AdminUsers')->getError();
                if (empty($error_word)) {
                    $this->success('编辑成功', U('AdminUser/edit', ['id' => $uid]));
                } else {
                    // 操作失败
                    $this->error($error_word);
                }
            }
        } else {
            $id = I('get.id', 0, 'intval');
            // 获取用户数据
            $user_data = M('AdminUsers')->find($id);
            // 获取已加入用户组
            $group_data = M('AuthGroupAccess')
                ->where(['uid' => $id])
                ->getField('group_id', true);
            // 全部用户组
            $data = D('AuthGroup')->select();
            $assign = [
                'data' => $data,
                'user_data' => $user_data,
                'group_data' => $group_data
            ];
            $this->assign($assign);
            $this->display();
        }
    }

    /**
     * 禁止/允许登陆
     */
    public function forbid() {
        $id = I('get.id');
        $status = I('get.status');
        if (empty($id) || !in_array($status, ['0', '1'])) {
            $this->error('操作失败');
        }
        D('AdminUsers')->updateData(['id' => $id], ['status' => $status]);
        $this->success('操作成功', U('AdminUser/index'));
    }
}
