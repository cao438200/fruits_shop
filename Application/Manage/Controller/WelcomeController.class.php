<?php
namespace Manage\Controller;
use Think\Controller;
class WelcomeController extends Controller {
    public function index(){
    	$start= date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));//当周起始时间
  		$end=date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));//当周结束时间
        $this->display();
    }
}