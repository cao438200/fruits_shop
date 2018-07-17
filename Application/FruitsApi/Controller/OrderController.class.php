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
        $type=I('post.type');
        if($float=='fruits_api_orderAll'){
            if($type==2){
                $map['status']=array(array('gt',0),array('lt',4));
            }else if($type==3){
                $map['status']=0;
            }else if($type==4){
                $map['status']=4;
            }
            $map['memberid']=$memberid;
            $order=M('order')
            ->field('status,code,createtime,useprice,Id,num,type,ps_status,refund_status')->Where($map)->select();
            foreach ($order as $key => $value) {
                $status=$value['status'];
                $type=$value['tpye'];
                $ps_status=$value['ps_status'];
                $refund_status=$value['refund_status'];
                if($status==0){
                    $order[$key]['sta']='待付款';
                }else if($status==1){
                    if($type==2){
                        $order[$key]['sta']='待自提';
                    }else{
                        if($ps_status=='0'){
                            $order[$key]['sta']='待配送';
                        }else if($ps_status=='20'){
                            $order[$key]['sta']='商家已接单';
                        }
                    }
                }else if($status==2){
                    $order[$key]['sta']='正在配送';
                }else if($status==3){
                    $order[$key]['sta']='订单已完成';
                }else if($status==4){
                    if($refund_status==0){
                        $order[$key]['sta']='退款中';
                    }else if($refund_status==1){
                        $order[$key]['sta']='退款成功';
                    }else if($refund_status==2){
                        $order[$key]['sta']='退款失败';
                    }
                }else if($status==5){
                    $order[$key]['sta']='订单已超时';
                }

                $orderid=$value['Id'];
                $order[$key]['desc']=M('commodity')
                ->alias('a')->field('a.Id,a.comdName,a.img_src,b.sp_num')
                ->join('order_details b on a.Id=b.goods_id')
                ->Where("b.orderid=$orderid")
                ->select();
            }
            if($order){
                $this->ajaxReturn($order,'JSON'); 
            }else{
                $this->ajaxReturn(0,'JSON');
            } 
        } else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

    public function delOrder(){//取消订单
        $float=I('post.op');
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_delOrder'){
            $id=I('post.id');//订单id
            if($id){
                $bool=M('order')->Where("Id=$id")->delete();
                M('order_details')->Where("orderid=$id")->delete();
                if($bool){
                    $msg=array('flag'=>'1','msg'=>'成功');
                }else{
                    $msg=array('flag'=>'1','msg'=>'失败');
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
            $id=I('post.id');//订单id
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
                    $msg=array('flag'=>1,'id'=>$orderid['Id']); 
                    $this->ajaxReturn($msg,'JSON');//返回订单id
                }else{
                    $msg=array('flag'=>-1,'error'=>'添加订单失败'); 
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


     public function refundOrder(){//申请退款
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_refundOrder'){
            $id=I('post.id');//订单id
            $map=array('status'=>4,'refund_status'=>0);
            $bool=M('order')->Where("Id=$id")->save($map);
            if($bool){
                $msg=array('flag'=>1,'msg'=>'成功');
            }else{
                $msg=array('flag'=>-1,'msg'=>'失败');
            }
        }else{
            $msg=array('error'=>'身份验证失败');
        }
        $this->ajaxReturn($msg,'JSON');
    }

    public function overtimeOrder(){//订单超时
        $float=I('post.op');
                if($float=='fruits_api_overtimeOrder'){
                    $id=I('post.id');
                    if($id){
                        $order=M('order')->Where("Id=$id")->save('status=5');//修改订单状态
                        if($order){
                            $msg=array('flag'=>'1','msg'=>'成功');
                        }else{
                            $msg=array('flag'=>'-1','msg'=>'失败');   
                        }
                    }else{
                        $msg=array('error'=>'缺少参数');
                    }
                }else{
                $msg=array('error'=>'身份验证失败');  
                }
                $this->ajaxReturn($msg,'JSON');
    }
}