<?php

/**
 * 产品控制器
 *
 * @author kid
 */
namespace Applogin\Controller;
use Applogin\Controller\AdminBaseController;

class FileController extends BaseController {

    public function upload(){
        if($_FILES['upload_file']['error'] > 0){
            print_r(json_encode(array("code"=>"1002", "data"=>'',"msg"=>'文件加载失败！')));
            exit;

        }
        if($_FILES['upload_file']['size'] > 1000000){
            print_r(json_encode(array("code"=>"1003", "data"=>'',"msg"=>'文件大小超过服务器限制')));
            exit;
        }

        if($_FILES['upload_file']['type']!='image/jpeg' && $_FILES['avatar']['type']!='image/gif'){
            print_r(json_encode(array("code"=>"1004", "data"=>'',"msg"=>'文件不是JPG或者GIF图片！')));
            exit;
        }
        $filetype = $_FILES['upload_file']['type'];
        if($filetype == 'image/jpeg'){
            $type = '.jpg';
        }
        if($filetype == 'image/gif'){
            $type = '.gif';
        }

        $cover_url = stripslashes($this->_getImgUrl());//保存本地


        if($cover_url){
            print_r(json_encode(array("code"=>"0", "data"=>$cover_url)));
            exit;
        }else{
            print_r(json_encode(array("code"=>"1005", "data"=>'',"msg"=>'上传失败')));
            exit;
        }

    }

    private function _getImgUrl() {
        import('Think.Upload');
        $config = C('UP_IMG_CONFIG');
        $upload = new \Think\Upload($config);
        $result = $upload->upload();
        return($config['dbPath'] . $result['upload_file']['savepath'] . $result['upload_file']['savename']);
    }
}
