<?php

namespace Applogin\Controller;
use Applogin\Controller\AdminBaseController;

/**
 * 后台首页控制器
 */
class IndexController extends BaseController {

    /**
     * 首页
     */
    public function index() {
        // 分配菜单数据
        $nav_data = D('AdminNav')->getTreeData('level', 'order_number,id');
        $assign = array(
            'data' => $nav_data
        );
        $this->assign($assign);
        $this->assign("user", session("user"));
        $this->display();
    }

    /**
     * elements
     */
    public function welcome() {

        $this->display();
    }

    /**
     * 修改密码
     */
    public function modPw() {
        $user = session("user");
        $data = I('post.');
        if (mb_strlen($data['new_pw']) < 6) {
            $this->error('密码必须在6个字符以上！');
        }
        if ($data['new_pw'] !== $data['cfm_pw']) {
            $this->error('两次输入不一致！');
        }
        if (!empty($data['old_pw'])) {
            $model = D('AdminUsers');
            $password = $model->where(array('id' => $user['id']))->field('password,salt')->find();
            if (!empty($password) && (md5($data['old_pw'] . $password['salt']) == $password['password'])) {
                $update['salt'] = rand_string(15);
                $update['password'] = md5($data['new_pw'] . $update['salt']);
                $model->updateData(array('id' => $user['id']), $update);
                $this->success('操作成功');
                return;
            }
        }
        $this->error('验证失败');
    }
}
