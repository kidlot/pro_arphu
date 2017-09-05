<?php

namespace Backstage\Controller;
use Backstage\Controller\BaseController;

/**
 * admin 基类控制器
 */
class AdminBaseController extends BaseController {

    /**
     * 初始化方法
     */
    public function _initialize() {
        parent::_initialize();
        $auth = new \Think\Auth();
        //$rule_name=MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $rule_name = CONTROLLER_NAME . '/' . ACTION_NAME;

        $result = $auth->check($rule_name, get_uid());
        if (!$result) {
            $this->error('您没有权限访问');
        } else {
            if (!IS_POST) {
                $AuthRule = D("AuthRule", "Model");
                $list = $AuthRule->getMenuName($rule_name);
                $count = count($list);
                if ($count == 0) {
                    $list[0] = array("title1" => "", "title2" => "", "title3" => "");
                }
                $this->assign("breadcrumbs", $list[0]);
            }
        }
    }
}
