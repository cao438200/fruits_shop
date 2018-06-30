<?php
namespace FruitsApi\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:*');  
// 响应头设置  
header('Access-Control-Allow-Headers:x-requested-with,content-type');   
class OrderController extends Controller {

    public function orderAll(){//全部订单
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_orderAll'){
            $map['memberid']=$memberid;
            $order=M('order')
            ->field('status,code,createtime,useprice,Id,num,type')->Where($map)->select();
            foreach ($order as $key => $value) {
                $orderid=$value['Id'];
                $order[$key]['desc']=M('commodity')
                ->alias('a')->field('a.comdName,a.img_src,b.sp_num')
                ->join('order_details b on a.Id=b.goods_id')
                ->Where("b.orderid=$orderid")
                ->select();
            }
            if($order){
                $this->ajaxReturn($order,'JSON'); 
            }else{
                $msg=array('data'=>'0','msg'=>'没有数据');
                $this->ajaxReturn($msg,'JSON');
            } 
        } else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

    public function orderPaid(){//已付款订单
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_orderPaid'){
            $map['memberid']=$memberid;
            $map['status']=array(array('gt',0),array('lt',4));
            $order=M('order')
            ->field('status,code,createtime,useprice,Id,num,type')->Where($map)->select();
            foreach ($order as $key => $value) {
                $orderid=$value['Id'];
                $order[$key]['desc']=M('commodity')
                ->alias('a')->field('a.comdName,a.img_src,b.sp_num')
                ->join('order_details b on a.Id=b.goods_id')
                ->Where("b.orderid=$orderid")
                ->select();
            }
             if($order){
                $this->ajaxReturn($order,'JSON'); 
            }else{
                $msg=array('data'=>'0','msg'=>'没有数据');
                $this->ajaxReturn($msg,'JSON');
            } 
        } else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

     public function orderObligation(){//待付款订单
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_orderObligation'){
            $map['memberid']=$memberid;
            $map['status']=0;
            $order=M('order')
            ->field('status,code,createtime,useprice,Id,num,type')->Where($map)->select();
            foreach ($order as $key => $value) {
                $orderid=$value['Id'];
                $order[$key]['desc']=M('commodity')
                ->alias('a')->field('a.comdName,a.img_src,b.sp_num')
                ->join('order_details b on a.Id=b.goods_id')
                ->Where("b.orderid=$orderid")
                ->select();
            }
             if($order){
                $this->ajaxReturn($order,'JSON'); 
            }else{
                $msg=array('data'=>'0','msg'=>'没有数据');
                $this->ajaxReturn($msg,'JSON');
            }  
        } else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }
    
    public function orderRefund(){//退款订单
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_orderRefund'){
            $map['memberid']=$memberid;
            $map['status']=4;
            $order=M('order')
            ->field('status,code,createtime,useprice,Id,num,type')->Where($map)->select();
            foreach ($order as $key => $value) {
                $orderid=$value['Id'];
                $order[$key]['desc']=M('commodity')
                ->alias('a')->field('a.comdName,a.img_src,b.sp_num')
                ->join('order_details b on a.Id=b.goods_id')
                ->Where("b.orderid=$orderid")
                ->select();
            }
            if($order){
                $this->ajaxReturn($order,'JSON'); 
            }else{
                $msg=array('data'=>'0','msg'=>'没有数据');
                $this->ajaxReturn($msg,'JSON');
            }  
        } else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

}