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
class GoodController extends Controller {

    public function goodDetails (){//商品详情
        $float=I('post.op');
        $goodid=I('post.id');//商品id
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_goodDetails'){
            $map['Id']=$goodid;
            $data=M('commodity')->field('comdName,retailPrice,tradePrice,img_src,desc,menuid')->Where($map)->find();
            $this->ajaxReturn($data,'JSON');
        }  else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }
    
    public function goodMenu (){//商品菜单
        $float=I('post.op');
        $goodid=I('post.id');//商品id
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_goodMenu'){
            $map['Id']=$goodid;
            $menuid=M('commodity')->field('menuid')->Where($map)->find();
            $menuid=json_decode($menuid['menuid']);
            // var_dump($menuid);
            for ($i=0; $i <count($menuid) ; $i++) { 
                $menu[$i]=M('menu')->field('Id,name,menu_main,first_minor,se_minor')->Where("Id=$menuid[$i]")->find();
            }
            $this->ajaxReturn($menu,'JSON');
        }  else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

    public function menu(){
        $float=I('post.op');
        $menuid=I('post.id');//菜单id
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_menu'){
            $map['Id']=$menuid;
            $data=M('menu')->field('desc')->Where($map)->find();
            $this->ajaxReturn($data,'JSON');
        }else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

}