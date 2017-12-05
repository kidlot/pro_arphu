<?php
namespace Applogin\Controller;
use Think\Controller;
/**
 * Base基类控制器
 */
class BaseController extends Controller{

    /**
     * 初始化方法
     */
    public function _initialize(){
        if (!check_login()) {
            $this->error('您还没有登录',"javascript:window.parent.location.href='".U('Login/index')."';");
        }
    }


}
