<?php
namespace Manage\Controller;
use Think\Controller;
class WelcomeController extends Controller {
    public function index(){
       // $url='http://124.225.146.25:3003/crm.asmx/Get_CardIDByAdd';
       //                echo   $cardID=$this->get_port($url);die;
    	$url='http://www.fruits.com/index.php/FruitsApi/Member/memberRegister';
  		$data=array(
  			'op'=>'fruits_api_memberRegister',
        'phone'=>'15712364561',
        'birthday'=>'2010-11-12',
        'sVIPName'=>'储层0',
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
    	// 	$start= date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));//当周起始时间
  		// $end=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));//当周结束时间
        $this->display();
    }
}