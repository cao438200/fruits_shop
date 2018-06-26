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
class MemberController extends Controller {

    public function myself(){//我的
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_myself'){
            $map['Id']=$memberid;
            $member=M('member')->field('sHealImgURL,sWXName,sVIPName,balance,re_integral')->Where($map)->find();
            $this->ajaxReturn($member,'JSON');
        }  else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }
    
}