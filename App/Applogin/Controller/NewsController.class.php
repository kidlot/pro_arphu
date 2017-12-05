<?php

/**
 * 产品控制器
 *
 * @author kid
 */
namespace Applogin\Controller;
use Applogin\Controller\AdminBaseController;

class NewsController extends BaseController {

    /**
     * 主页
     */
    public function index() {
        $news = M('News')->select();
        $assign = array(
            'data' => $news
        );
        $this->assign($assign);
        $this->display();
    }

    public function add() {
        $data = array(
            'title' => I('edit_news_title'),
            'url' =>I('edit_url'),
            'pic_url' =>I('edit_news_pic_url'),
            'contents' =>I('content2')
        );

        M("News")->add($data);

        $this->success('添加成功', U('News/index'));
    }

    public function edit() {
        $id = I("id");
        if(empty($id)){
            $this->error('请先选择新闻');
        }
        $map = array('id'=>$id);
        $data = array(
            'title' => I('edit_news_title'),
            'url' =>I('edit_url'),
            'pic_url' =>I('edit_news_pic_url'),
            'contents' =>I('content1')
        );
        M("News")->where($map)->save($data);
        $this->success('修改成功', U('News/index'));
    }

    public function delete() {
        $id = I("id");
        if(empty($id)){
            $this->error('请先选择新闻');
        }
        $map = array('id'=>$id);
        M("News")->where($map)->delete();
        $this->success('删除成功', U('News/index'));
    }
}
