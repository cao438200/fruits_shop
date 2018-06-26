<?php
namespace FruitsApi\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:*');  
// 响应头设置  
header('Access-Control-Allow-Headers:x-requested-with,content-type');   
class IndexController extends Controller {
    public function goodsClass(){//商品分类接口
    	$float=I('post.op');
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_index'){
    		$goods_class=M('commodity_class')->select();
       	 	$this->ajaxReturn($goods_class,'JSON');
    	}  else{
    		$this->ajaxReturn($msg,'JSON');
    	}  
        
    }

    public function goods(){//首页商品接口
    	$float=I('post.op');
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_goods'){
    		$map['status']=1;
    		$goods=M('commodity')->field('Id,comdName,img_src,retailPrice,tradePrice')->Where($map)->order('encode desc')->limit(0,6)->select();
    		$this->ajaxReturn($goods,'JSON');
    	}else{
    		$this->ajaxReturn($msg,'JSON');
    	}
    }

    public function banner(){//首页banner图
    	$float=I('post.op');
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_banner'){
    		$banner=M('banner')->Where('status=1')->order('sort desc')->select();
    		$this->ajaxReturn($banner,'JSON');
    	}else{
    		$this->ajaxReturn($msg,'JSON');
    	}
    }
}