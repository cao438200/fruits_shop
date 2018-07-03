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

    public function delOrder(){//取消订单
        $float=I('post.op');
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_delOrder'){
            $id=I('post.id')//订单id
            if($id){
                $bool=M('order')->Where("Id=$id")->del();
                M('order_details')->Where("orderid=$id")->del();
                if($bool){
                    $msg=array('data'=>'1','msg'=>'成功');
                }else{
                    $msg=array('data'=>'1','msg'=>'失败');
                }
                $this->ajaxReturn($msg,'JSON'); 
            }else{
                $msg=array('error'=>'缺少参数'); 
                $this->ajaxReturn($msg,'JSON'); 
            }
        }else{
            $this->ajaxReturn($msg,'JSON');
        }
    }

    public function recurOrder(){//再来一单
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_recurOrder'){
            $id=I('post.id')//订单id
            if($id){
                $order=M('order')->Where("Id=$id")->find();
                $order_desc=M('order_details')->Where("orderid=$id")->select();
                $code=$this->createNonceStr()."$memberid";
                $map=array(
                    'memberid'=>$memberid,
                    'code'=>$code,
                    'createtime'=>date("Y-m-d H:i:s"),
                    'yh_price'=>$oder['yh_price'],
                    'useprice'=>$oder['useprice'],
                    'addressid'=>$oder['addressid'],
                );
                $bool=M('order')->add($map);//添加订单
                if($bool){
                    $orderid=M('order')->field('Id')->Where("code='$code'")->find();
                    foreach($order_desc as $key => $value){
                        $maps=array(
                            'orderid'=>$orderid['Id'],
                            'goods_id'=>$value['goods_id'],
                            'goods_name'=>$value['goods_name'],
                            'sp_num'=>$value['sp_num'],
                            'sp_price'=>$value['sp_price'],
                        );
                        M('order_details')->add($maps);//添加订单详情
                    }
                    $this->ajaxReturn($orderid,'JSON');//返回订单id
                }else{
                    $msg=array('error'=>'添加订单失败'); 
                    $this->ajaxReturn($msg,'JSON');
                }
            }else{
                $msg=array('error'=>'缺少参数'); 
                $this->ajaxReturn($msg,'JSON'); 
            }
        }else{
            $this->ajaxReturn($msg,'JSON');
        }
    }

}