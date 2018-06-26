<?php
namespace Manage\Controller;
use Think\Controller;
class WelcomeController extends Controller {
    public function index(){
    	$url='http://www.fruits.com/index.php/FruitsApi/Member/myself';
		$data=array(
			'op'=>'fruits_api_myself',
		);
		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1);  
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);$result=json_decode($result);
        curl_close($curl);
	    var_dump($result);die;
	    // $maps=array(
            //     'memberid'=>1,
            //     'goods_id'=>$cart['Id'],
            //     'goods_num'=>$num,
            //     'group_name'=>$cart['GroupName'],
            //     'price'=>$price,
            //     'createtime'=>date("Y-m-d H:i:s"),
            //     'group_id'=>$cart['groupid'],
            // )
            // $bool=M('shopping_cart')->add($maps);
            // if($bool){
            //     $ts['msg']='1';//添加成功
            // }else{
            //     $ts['msg']='2';//添加失败
            // }
    // 	$start= date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));//当周起始时间
  		// $end=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));//当周结束时间
        $this->display();
    }
}