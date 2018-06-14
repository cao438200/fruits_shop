<?php
namespace Home\Controller;
use Think\Controller;
class CaipiaoSourceController extends Controller {
    public function index(){
    	 $links=M('alink')->where(array('dd'=>1))->order('id desc')->select();//友情链接

        $this->assign('links',$links);
        $this->display();
    }
}