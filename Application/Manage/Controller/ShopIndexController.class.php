<?php

namespace Manage\Controller;
use Think\Controller;
class ShopIndexController extends Controller {
    public function index(){
    	$zong_price=M('order')->field('sum(useprice) as price')->Where('status=3')->find();//总销售额
    	$this->assign('zong_price',$zong_price);
    	$day=date("Y-m-d");
    	$map1['status']=array('eq',3);
    	$map1['createtime']=array('between',"$day,$day 23:59:59");
    	$jr_price=M('order')->field('sum(useprice) as price')->Where($map1)->find();//今儿销售额
    	$this->assign('jr_price',$jr_price);
    	$zong_order=M('order')->count();//总订单数
    	$this->assign('zong_order',$zong_order);
    	$map2['createtime']=array('between',"$day,$day 23:59:59");
    	$jr_order=M('order')->Where($map2)->count();//今儿订单数
    	$map3['status']=array('eq',4);
    	$map3['refund_status']=array('eq',0);
    	$dtk_order=M('order')->Where($map3)->count();//待退款订单数
    	$this->assign('jr_price',$jr_price);
       	$start= date("Y-m-d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));//当周起始时间
        for ($i=0; $i <7 ; $i++) { //循环当前周时间
            $time= date("Y-m-d",strtotime("+$i day",strtotime("$start")));
            $map['createtime']=array('between',"$time,$time 23:59:59");
            $count1=M('order')->Where($map)->count();
            $data1 .="$count1";
            $data1 .=",";
            $map['status']=array('eq',3);
            $count2=M('order')->Where($map)->count();
            $data2 .="$count2";
            $data2 .=",";
            $map['status']=array('neq',3);
            $count3=M('order')->Where($map)->count();
            $data3 .="$count3";
            $data3 .=",";
        }
    	// $data1='110, 105, 160, 130, 110, 121, 100';
    	// $data2='100, 100, 120, 110, 100, 101, 90';
    	// $data3='0, 5, 10, 0, 15, 12, 6';
        $sales=M('order_details')->alias('a')
        ->field("sum(a.sp_num) as num,b.comdName,b.comdNum,b.FcomdType")
        ->join('commodity b on a.goods_id=b.Id')
        ->group('a.goods_id')
        ->order('num desc')
        ->limit('0,15')
        ->select();
        $this->assign('sales',$sales);
    	$this->assign('data1',$data1);
    	$this->assign('data2',$data2);
    	$this->assign('data3',$data3);
        $this->display();
    }

}