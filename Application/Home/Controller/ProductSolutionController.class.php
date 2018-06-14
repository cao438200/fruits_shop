<?php
namespace Home\Controller;
use Think\Controller;
class ProductSolutionController extends Controller {
   public function index(){
    //网站优化
        $website=M('lianxi')->where(array('id'=>2))->find();
        $this->assign('website',$website);

         $links=M('alink')->where(array('dd'=>1))->order('id desc')->select();//友情链接

        $this->assign('links',$links);
       

        $this->assign('id',$id);
        //输出
        $this->display();
    }

}