<?php

namespace Applogin\Controller;
use Applogin\Controller\AdminBaseController;

/**
 * 查看登陆日志
 */
class AdminLogController extends AdminBaseController {

    /**
     * 菜单列表
     */
    public function index() {
        $page_size = C('DEFAULT_PAGE_SIZE');
        $ip = I('get.ip');
        $name = I('get.name');

        $admin_log = D('AdminLog');
        $list = $admin_log->SelectLogData(0);
        $this->assign('data', $list); // 赋值数据集
        $count = $admin_log->SelectLogData(1); // 查询满足要求的总记录数
        $Page = new \Think\Page($count, $page_size); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出
        //搜索参数回传
        if (!empty($ip)) {
            $search['ip'] = $ip;
        }
        if (!empty($name)) {
            $search['name'] = $name;
        }
        $this->assign('page', $show); // 赋值分页输出
        $this->assign('search', $search); // 赋值分页输出
        $this->display();
    }
}
