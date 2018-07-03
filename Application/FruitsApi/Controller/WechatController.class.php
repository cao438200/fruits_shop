<?php
namespace FruitsApi\Controller;
use Think\Controller;

class WechatController extends Controller {
    public function index()  
    {  
      	session_start();
      	$memberid=$_SESSION['memberid'];//判断是否有登陆
      	if(!$memberid){
      		$appid='wx16eada8cf1729fc4';//公众号appid
			$redirect_uri = urlencode ( 'http://f5tdyq.natappfree.cc/fruits_shop/index.php/FruitsApi/Wechat/getWxMember' );//获取openid页面
			$url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
      	}else{
      		$url="http://f5tdyq.natappfree.cc/fruits_shop/index.php/FruitsApi/Wechat/dl";//有登陆信息直接跳转到指定页面
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
		$member=M('member')->Where("sWXOpenID='$openid'")->find();//判断是否获取过openid
		if($member){
			session_start();
			$_SESSION['memberid']=$member['Id'];//储层用户id
			$_SESSION['openid']=$openid;//储层用户openid
			$url="http://f5tdyq.natappfree.cc/fruits_shop/index.php/FruitsApi/Wechat/zc1";
			header("Location:".$url);
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
			$bool=M('member')->add($map);//添加用户微信信息
			if($bool){
				$member=M('member')->field("Id")->Where("sWXOpenID='$openid'")->find();
				session_start();
				$_SESSION['memberid']=$member['Id'];//储层用户id
				$_SESSION['openid']=$openid;//储层用户openid
				$url="http://f5tdyq.natappfree.cc/fruits_shop/index.php/FruitsApi/Wechat/zc2";
				header("Location:".$url);
			}else{
				echo '用户openid储层失败';
			}
			
		}  	 
    } 

    public function dl(){
    	$openid='oxxbg0pOu5IvNgpaz1qWDZfHaOo0';
    	$token=$this->get_access_token();
    	$data=[
            'touser'=>$openid,
            'template_id'=>'FJ2tNIRv6by28GmMeh54pfowwaJBkb4gRop9m5_rzEU',
            'url'=>"http://weixin.qq.com/download",
            'topcolor'=>"#FF0000",
            'data'=>array(
            	'f'=>array('value'=>'你好黄先生',"color"=>"#173177"),
                'keynote1'=>array('value'=>'消费人民币260.00元',"color"=>"#173177"),
                'keynote2'=>array('value'=>'生鲜超市',"color"=>"#173177"),
                'keynote3'=>array('value'=>date("Y-m-d h:i:s",time()),"color"=>"#173177"),
                'remark'=>array('value'=>'感谢你的使用',"color"=>"#173177")
            )
        ];
        $data=json_encode($data);
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		curl_close($ch);
        $this->ajaxReturn($output,'JSON');
    } 

    public function zc1(){
    	echo 'zc1';
    } 

    public function zc2(){
    	echo 'zc2';
    } 
}