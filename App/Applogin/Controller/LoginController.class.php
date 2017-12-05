<?php

namespace Applogin\Controller;
use Think\Controller;
use Org\Util\Image as Image;

class LoginController extends Controller {

    public function index() {
        if (check_login()) {
            $this->success('登录成功、前往管理后台', U('Index/index'));
        } else {
            $this->display("Index/login");
        }
    }

    //验证码
    public function verify() {
        $type = I('type', 'gif');
        Image::buildImageVerify(4, 1, $type);
    }

    /**
     * 验证用户登录session($verifyName
     * @return [type] [description]
     */
    public function checkLogin() {
        $username = trim(I("post.account"));
        $password = trim(I("post.password"));
        $verify = trim(I("post.verify"));
        if (empty($username) || empty($password) || empty($verify)) {
            $this->error('账号|密码|验证码都必填！', U("index"));
        }
        //验证验证码是否正确
        if (session('verify') != md5($verify . C("VERIFY_CONFLATION"))) {
            $this->error('验证码错误！', U("index"));
        }
        //验证用户信息
        $Users = D("AdminUsers", "Model");
        $map["username"] = $username;
        $user = $Users->getData($map);
        if ($user) {
            if (1 == $user['status']) {
                $md5Password = md5($password . $user['salt']);
                if ($md5Password == $user['password']) {
                    //设置session 有效期
                    //ini_set ( 'session.gc_maxlifetime',1);
                    session('user', $user);
                    //记录登录信息
                    $data["last_login_ip"] = get_client_ip();
                    $data["last_login_time"] = time();
                    $data["login_count"] = array('exp', '`login_count`+1');
                    $Users->updateData(['id' => $user['id']], $data);
                    //增加登录日志
                    addAdminLog("登录成功！");
                    $this->success('登录成功、前往管理后台', U('Index/index'));
                } else {
                    $this->error('密码不正确！', U('index'));
                }
            } else {
                $this->error('帐号已禁用！', U('index'));
            }
        } else {
            $this->error('帐号不存在！', U('index'));
        }
    }

    public function logout() {
        session(null);
        session_destroy();
        $this->success('退出登录成功', U('Login/index'));
    }
}