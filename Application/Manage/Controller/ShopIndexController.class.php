<?php

namespace Manage\Controller;
use Think\Controller;
class ShopIndexController extends Controller {
    public function index(){
    	$data1='110, 105, 160, 130, 110, 121, 100';
    	$data2='100, 100, 120, 110, 100, 101, 90';
    	$data3='0, 5, 10, 0, 15, 12, 6';
    	$this->assign('data1',$data1);
    	$this->assign('data2',$data2);
    	$this->assign('data3',$data3);
        $this->display();
    }

}