<?php

namespace Manage\Controller;
use Think\Controller;

class OrderTkController extends Controller {

    public function index(){
    	$search=I('post.search');//是否有搜索条件
    	$map['order.status']=array('eq',4);//订单状态退款
    	$num0=M('order')->Where($map)->count();
    	$this->assign('num0',$num0);
    	$map['order.refund_status']=array('eq',0);//待退款订单数
    	$num1=M('order')->Where($map)->count();
    	$this->assign('num1',$num1);
    	$map['order.refund_status']=array('eq',1);//已退款订单数
    	$num2=M('order')->Where($map)->count();
    	$this->assign('num2',$num2);
    	$map['order.refund_status']=array('eq',2);//未退款订单数
    	$num3=M('order')->Where($map)->count();
    	$this->assign('num3',$num3);
    	if($search){
    		$op=I('post.ops');//订单状态下搜索
    		if($op=='order_0' || !$op){
    			$map['order.refund_status']=array('egt',0);//所有退款订单
    		}elseif($op=='order_1'){
    			$map['order.refund_status']=array('eq',0);//待退款订单
    		}elseif($op=='order_2'){
    			$map['order.refund_status']=array('eq',1);//已退款订单
    		}elseif($op=='order_3'){
                $map['order.refund_status']=array('eq',2);//未退款订单
            }
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
    			$map['order.refund_status']=array('egt',0);//所有退款订单
    		}elseif($op=='order_1'){
    			$map['order.refund_status']=array('eq',0);//待退款订单
    		}elseif($op=='order_2'){
    			$map['order.refund_status']=array('eq',1);//已退款订单
    		}elseif($op=='order_3'){
    			$map['order.refund_status']=array('eq',2);//未退款订单
    		}
    	}
        $count=M('order')
        ->join('LEFT JOIN member on order.memberid=member.Id')
        ->Where($map)
        ->count();
        $p = getpage($count,10);//分页
        $this->assign('page',$p->show());
    	$orderps_all=M('order')
    	->field('member.sVIPName,order.code,order.createtime,order.paytime,order.useprice,order.yh_price,order.refund_status,order.remark,order.endtime,order.Id')
    	->join('LEFT JOIN member on order.memberid=member.Id')
    	->Where($map)->order('order.createtime desc')
        ->limit($p->firstRow.','.$p->listRows)
    	->select();
    	$this->assign('orderps_all',$orderps_all);
    	$this->assign('op',$op);
    	$this->display();
    }

    public function audit(){
    	$id=I('post.id');
    	$status=I('post.status');
        if($status==1){//确认退款修改用户余额
            $price=M('order')->field('delivery,useprice,memberid')->Where("Id=$id")->find();
            $useprice=$price['delivery']+$price['useprice'];
            $memberid=$price['memberid'];
            $balance=M('member')->field('balance')->Where("Id=$memberid")->find();
            $maps['balance']=$balance['balance']+$useprice;
            M('member')->Where("Id=$memberid")->save($maps);
        }
    	$map['refund_status']=$status;
    	M('order')->Where("Id=$id")->save($map);//修改退款状态
    }


}