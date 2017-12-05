<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 默认首页Controller
 * 显示框架基本信息，具体项目请新建模块
 */
class EnquiryController extends BaseController{

       public function submit_enquiry(){
           $data['describe'] = I('describe');
           $data['first_name'] = I('first_name');
           $data['last_name'] = I('last_name');
           $data['company_name'] = I('company_name');
           $data['email'] = I('email');
           $data['phone'] = I('phone');
           if("" == $data['describe']){
               print_r(json_encode(array("code" => "1001", "data" => '', "msg" => "Describe your project is a required field.")));
               exit;
           }
           if("" == $data['email'] ){
               print_r(json_encode(array("code" => "1002", "data" => '', "msg" => "Email is a required field.")));
               exit;
           }
           $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
           if ( !preg_match( $pattern, $data['email'] ) ) {
               print_r(json_encode(array("code" => "1003", "data" => '', "msg" => "Incorrectemail format")));
               exit;
           }
           $re = M("Enquiry")->add($data);
           if($re){
               print_r(json_encode(array("code" => "0", "data" => '', "msg" => "Your form has been successfully submitted.")));
               exit;
           }else{
               print_r(json_encode(array("code" => "1004", "data" => '', "msg" => "Submit failed.")));
               exit;
           }
       }



}
