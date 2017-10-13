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
            $this->_empty();
            exit;
        }
        $id=$list['id'];
        $table=ucfirst($list['type']);

        switch($table){
            case 'Product':
                $template='photo';
                $num=C('LIST_PRONUM');
                break;

            case 'New':
                $template='index';
                $num=C('LIST_NEWNUM');
                break;

            case 'Photo':
                $template='photo';
                $num=C('LIST_PHOTONUM');
                break;

            case 'Download':
                $template='index';
                $num=C('LIST_DOWNNUM');
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
        }else{
            if($list['pid']!=0){
                if($indb=M('List')->field('id')->where('pid = '.$id)->select()){
                    foreach($indb as $v){
                        $inpid.=$v['id'].',';
                    }
                    $where['pid']=array('in',rtrim($id.",".$inpid,","));
                }else{
                    $where['pid']=array('eq',$id);
                }
                $list['id']=$list['pid'];
            }else{
                $where['bid']=array('eq',$id);
            }

            $db=M($table);
            $count=$db->where($where)->count();

            import('@.Org.Page');
            $pageurl = (C('URL_MODEL')==2) ? $list['url'] : '' ;
            $page=new Page($count,$num,'',$pageurl);
            //$prevs= (C('CNEN')=='cn') ? '上一页' : 'Previous' ;
            //$nexts= (C('CNEN')=='cn') ? '下一页' : 'Next' ;
            $page->setConfig('prev','«');
            $page->setConfig('next','»');
            $page->setConfig('theme',"%upPage% %first% %prePage% %linkPage% %nextPage% %end% %downPage%");
            $show=$page->show();

            $article=$db->where($where)->order('sort asc,id desc')->limit($page->firstRow.','.$page->listRows)->select();
            $this->assign('article',$article);
            $this->assign('page',$show);
            $this->assign('table',$table);
        }
        $this->assign('list',$list);
        $this->assign('bid',$bid);
        $this->display($template);
    }

}
