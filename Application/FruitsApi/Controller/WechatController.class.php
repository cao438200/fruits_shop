<?php
namespace FruitsApi\Controller;
use Think\Controller;

class WechatController extends Controller {
    public function index()  
    {  
      	session_start();
      	$memberid=$_SESSION['memberid'];
      	if(!$memberid){
      		$appid='wx16eada8cf1729fc4';
			$redirect_uri = urlencode ( 'http://f4zq8m.natappfree.cc/fruits_shop/index.php/FruitsApi/Wechat/getWxMember' );
			$url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
      	}else{
      		$url="http://f4zq8m.natappfree.cc/fruits_shop/index.php/FruitsApi/Wechat/dl";
      	}
      	header("Location:".$url);	
    }  
          
 	public function getWxMember()  
    {  
        $appid = "wx16eada8cf1729fc4";  
		$secret = "594bbd9d07c61822df5f9b8935e875d9";  
		$code = $_GET["code"];
		//第一步:取全局access_token
		$token = $this->get_access_token(); 
		//第二步:取得openid
		$oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
		$oauth2 = $this->https_request($oauth2Url);
		// 第三步:根据全局access_token和openid查询用户信息    
		$openid = $oauth2['openid'];
		$member=M('member')->Where("sWXOpenID='$openid'")->find();
		if($member){
			session_start();
			$_SESSION['memberid']=$member['Id'];//储层用户id
		}else{
			$get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$token&openid=$openid&lang=zh_CN";
			$userinfo = $this->https_request($get_user_info_url);
			$openid = $userinfo['openid'];
			$map=array(
				'sWXOpenID'=>$openid,
				'sWXName'=>$userinfo['nickname'],
				'sSex'=>$userinfo['sex'],
				'sHealImgURL'=>$userinfo['headimgurl'],
			);
			$bool=M('member')->add($map);
			if($bool){
				$member=M('member')->field("Id")->Where("sWXOpenID='$openid'")->find();
				session_start();
				$_SESSION['memberid']=$member['Id'];//储层用户id
				$url="http://f4zq8m.natappfree.cc/fruits_shop/index.php/FruitsApi/Wechat/zc";
				header("Location:".$url);
			}else{
				echo '用户openid储层失败';
			}
			
		}  	 
    } 

    public function dl(){
    	echo $_SESSION['memberid'];
    } 

    public function zc(){
    	echo 'zc';
    } 
}