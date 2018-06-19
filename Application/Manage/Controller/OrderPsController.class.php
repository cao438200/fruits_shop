<?php

namespace Manage\Controller;
use Think\Controller;

class OrderPsController extends Controller {

    public function index(){
    	$search=I('post.search');//是否有搜索条件
    	$map['order.type']=array('eq',1);
    	if($search){
    		$op=I('post.ops');//订单状态下搜索
    		$member_name=I('post.name');
    		$this->assign('member_name',$member_name);
    		$start_time=I('post.start_time');
    		$this->assign('start_time',$start_time);
    		if(!$start_time){
    			$start='2000-01-01';
    		}else{
    			$start=$start_time;
    		}
    		$end_time=I('post.end_time');
    		$this->assign('end_time',$end_time);
    		if(!$end_time){
    			$end='3000-01-01';
    		}else{
    			$end=$end_time;
    		}
    		if($member_name){
    			$map['order.createtime'] = array('between',"$start,$end 23:59:59");	    		
	    		$map['member.sVIPName|order.code']=array('like', "%$member_name%");
	    	}else{
	    		$map['order.createtime'] = array('between',"$start,$end 23:59:59");
	    	}
    	}else{
    		$op=I('get.op');//订单状态
    		if($op=='order_0' || !$op){
    			$map['order.status']=array('gt',0);//所有订单
    		}elseif($op=='order_1'){
    			$map['order.status']=array('eq',1);//代配送订单
    		}elseif($op=='order_2'){
    			$map['order.status']=array('eq',2);//正在配送订单
    		}elseif($op=='order_3'){
    			$map['order.status']=array('eq',3);//已完成订单
    		}
    	}
    	$orderps_all=M('order')->join('LEFT JOIN member on order.memberid=member.Id')->Where($map)->select();
    	$this->assign('orderps_all',$orderps_all);
    	$this->assign('op',$op);
    	$this->display();
    }


}