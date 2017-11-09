<?php

namespace Home\Controller;
use Think\Controller;

/**
 * 默认首页Controller
 * 显示框架基本信息，具体项目请新建模块
 */
class ListController extends BaseController{

    public function index(){
        $id=I('id',0,'intval');
        $this->doList($id,"id =%d");

    }
    public function html(){
        $url=I('url');
        $this->doList($url,"url ='%s'");
    }

    protected function doList($var,$str){
        $bid='';
        $list=M('list')->field('id,pid,bid,url,type,title,name,keywords,description,contents')->where($str,array($var))->find();
        $this->getid = ($list['pid']==0 && $list['type']!='page') ? 0 : $list['id'] ;
        if (!$list) {
            exit;
        }
        $id=$list['id'];
        $table=ucfirst($list['type']);
        switch($table){
            case 'Product':
                $template='photo';
                break;

            case 'News':
                $template='News/index';
                break;

            case 'Project':
                $template='Project/index';

                break;

            case 'Download':
                $template='index';
                break;

            case 'Contact':
                $template='contact';
                break;

            case 'About':
                $template='about';
                break;

            default:
                $template='index';
        }

        if($table == 'Contact'||$table == 'About'){
            //$this->pagelist=recursive(M('List')->field('id,url,pid,name')->where("type = 'page'")->order('sort')->select());
            $list['contents']=$list['contents'];
        }else if($table == 'News'){
            $news = M('News')->where(['status'=>1])->order("time desc")->select();
            $recent = M('News')->where(['status'=>1])->order("time desc")->limit(5)->select();
            $this->assign('news',$news);
            $this->assign('recent',$recent);
        }else if($table == 'Project'){
            $Project= M('Project')->where(['status'=>1])->select();
            $this->assign('project',$Project);
        }
        $this->assign('list',$list);
        $this->assign('bid',$bid);
        $this->display($template);
    }

}
