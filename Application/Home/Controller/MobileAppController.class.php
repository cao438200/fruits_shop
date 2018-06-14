<?php
namespace Home\Controller;
use Think\Controller;
class MobileAppController extends Controller {
    public function index(){
    	 $website=M('lianxi')->where(array('id'=>2))->find();
        $this->assign('website',$website);
         $links=M('alink')->where(array('dd'=>1))->order('id desc')->select();//友情链接

        $this->assign('links',$links);
        $this->display();
    }
}