<?php

namespace Manage\Controller;
use Think\Controller;

class CouponGetListController extends Controller {

    public function index(){
    	$count=M('member_coupons')
    		->field('member.sVIPName,member.sIDCard,member_coupons.status,member_coupons.lq_time,coupons.type,coupons.desc,coupons.valid_time')
    		->join('member on member_coupons.member_id=member.id')
    		->join('coupons on member_coupons.coupons_id=coupons.id')
    		->order('member_coupons.lq_time')
    		->count();
    	$p = getpage($count,1);//分页
	    $this->assign('page',$p->show());
	    $list=M('member_coupons')
    		->field('member.sVIPName,member.sIDCard,member_coupons.Id,member_coupons.status,member_coupons.lq_time,coupons.type,coupons.desc,coupons.valid_time')
    		->join('member on member_coupons.member_id=member.id')
    		->join('coupons on member_coupons.coupons_id=coupons.id')
    		->order('member_coupons.lq_time')
    		->limit($p->firstRow.','.$p->listRows)
    		->select();
    	$this->assign('list',$list);
    	foreach ($list as $key => $value) {//自动修改优惠券状态
    		$day1= date('Y-m-d H:i:s');
    		$day2=$value['lq_time'];
    		$days=$this->getDays($day1,$day2);//获取时间天数差
    		if($days>$value['valid_time']){//判断优惠券是否失效
    			$id=$value['Id'];
    			$save['status']=3;
    			M('member_coupons')->Where("Id=$id")->save($save);
    		}
    	}
    	//新用户优惠券领取人数
    	$map1['status']=array('gt',0);
    	$map1['type']=array('eq',1);
    	$num1=M('member_coupons')->Where($map1)->count();
    	$this->assign('num1',$num1);
    	//新用户优惠券已使用人数
    	$map1['status']=array('eq',2);
    	$map1['type']=array('eq',1);
    	$num2=M('member_coupons')->Where($map1)->count();
    	$this->assign('num2',$num2);
    	//新用户优惠券未使用人数
    	$map1['status']=array('eq',1);
    	$map1['type']=array('eq',1);
    	$num3=M('member_coupons')->Where($map1)->count();
    	$this->assign('num3',$num3);
    	//特殊日优惠券领取人数
    	$map1['status']=array('gt',0);
    	$map1['type']=array('eq',2);
    	$num4=M('member_coupons')->Where($map1)->count();
    	$this->assign('num4',$num4);
    	//特殊日优惠券已使用人数
    	$map1['status']=array('eq',2);
    	$map1['type']=array('eq',2);
    	$num5=M('member_coupons')->Where($map1)->count();
    	$this->assign('num5',$num5);
    	//特殊日优惠券未使用人数
    	$map1['status']=array('eq',1);
    	$map1['type']=array('eq',2);
    	$num6=M('member_coupons')->Where($map1)->count();
    	$this->assign('num6',$num6);

    	$this->display();
    }


}