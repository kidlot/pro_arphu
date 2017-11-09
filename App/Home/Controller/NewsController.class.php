<?php

namespace Home\Controller;
use Think\Controller;


class NewsController extends BaseController{


    public function detail()
    {
        $url=I('url');
        $detail = M('News')->where(['url'=>$url])->find();
        $this->assign('detail',$detail);
        $this->display();

    }


}
