<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $website=M('lianxi')->where(array('id'=>2))->find();
        $this->assign('website',$website);
	//友情链接
        $links=M('alink')->where(array('dd'=>1))->order('id desc')->select();
        $this->assign('links',$links);
    //资讯分类
    	$types=M('bigad2')->where(array('dd'=>1))->order('id asc')->field('id')->select();
    	$this->assign('types',$types);
	//查询最新的产品资讯
    	$articles_1 = M('article')->where(array('dd'=>1,'aid'=>$types[0]['id']))->order('id desc')->limit(0,3)->select();
    	$this->assign('articles_1',$articles_1);
    //查询最新的行业新闻
    	$articles_2 = M('article')->where(array('dd'=>1,'aid'=>$types[1]['id']))->order('id desc')->limit(0,3)->select();
    	$this->assign('articles_2',$articles_2);
        $this->display();
    }
}