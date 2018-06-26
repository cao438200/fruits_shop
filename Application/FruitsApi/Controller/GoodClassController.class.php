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
class GoodClassController extends Controller {

    public function className(){//分类名
        $float=I('post.op');
        $classid=I('post.id');//分类id
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_className'){
            $map['Id']=$classid;
            $className=M('commodity_class')->field('GroupName')->Where($map)->find();
            $this->ajaxReturn($className,'JSON');
        }  else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }
    
    public function classGoods(){//分类商品接口
    	$float=I('post.op');
        $classid=I('post.id');//分类id
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_classGoods'){
            $map['groupid']=$classid;
            $map['status']=1;
    		$goods=M('commodity')->field('Id,comdName,img_src,retailPrice,tradePrice')->Where($map)->order('encode desc')->select();
       	 	$this->ajaxReturn($goods,'JSON');
    	}  else{
    		$this->ajaxReturn($msg,'JSON');
    	}  
        
    }
}