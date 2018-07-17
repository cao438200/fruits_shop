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
class MemberPayController extends Controller {

	public function pay(){//点击确认支付
		$float=I('post.op');
                $memberid=1;
                if($float=='fruits_api_pay'){
                	$id=I('post.id');//订单id
                        $zf_type=I('post.zf_type');//判断是余额支付还是微信支付1.微信支付2.余额支付
                	$order_type=I('post.type');//订单类型
                	$appointment_time=I('post.appointment_time');//预约时间
                	$price=I('post.price');//支付金额
                        if($id && $zf_type && $order_type){
                                if($zf_type==1){//微信支付

                                                
                                }else if($zf_type==2){//余额支付
                                        $balance=M('member')->field('balance')->Where("$id=$memberid")->find();
                                        if($balance['balance']<$price){
                                              $msg=array('flag'=>'0','msg'=>'余额不足');  
                                        }else{

                                        }
                                }
                        }else{
                                $msg=array('error'=>'缺少参数');
                        }
                	
                }else{
                	$msg=array('error'=>'身份验证失败');
                }
                $this->ajaxReturn($msg,'JSON');
	}

                                       
	public function cartPay(){//购物车点击支付
		$float=I('post.op');
                $memberid=1;
                $msg=array('error'=>'身份验证失败');
                if($float=='fruits_api_cartPay'){
                	//购物车支付
                	$id=I('post.id');//购物车id
                	$yh_price=I('post.yh_price');//优惠价
                	$zf_price=I('post.zf_price');//支付价
                	$address=M('member_address')->field('Id')->Where("memberid=$memberid")->order('Id desc')->find();
                	if($id && $yh_price && $zf_price){
                		$code=$this->createNonceStr()."$memberid";
                		$map=array(
                			'memberid'=>$memberid,
                			'code'=>$code,
                			'createtime'=>date("Y-m-d H:i:s"),
                			'yh_price'=>$yh_price,
                			'useprice'=>$zf_price,
                			'addressid'=>$address['Id'],
                		);
                		$bool=M('order')->add($map);//添加订单
                		if($bool){
                			$orderid=M('order')->field('Id')->Where("code=$code")->find();//查订单id
                			for($i=0;$i<count($id);$i++){
        		        		$order=M('commodity')->alias('a')
        		        		->field('a.comdName,a.Id,b.goods_num,b.price')
        		        		->join('shopping_cart b on a.Id=b.goods_id')
        		        		->Where("b.Id=$id[$i]")
        		        		->find();
        		        		$maps=array(
        		        			'orderid'=>$orderid['Id'],
        		        			'goods_id'=>$order['Id'],
        		        			'goods_name'=>$order['comdName'],
        		        			'sp_num'=>$order['goods_num'],
        		        			'sp_price'=>$order['price'],
        		        		);
        		        		M('order_details')->add($maps);//添加订单详情
                			}
                                        $msg=array('flag'=>'1','orderid'=>$orderid['Id']);
                		}else{
                			$msg=array('flag'=>'-1','error'=>'订单添加失败');
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

        public function promptlyPay(){//立即支付
                $float=I('post.op');
                $memberid=1;
                $msg=array('error'=>'身份验证失败');
                if($float=='fruits_api_cartPay'){
                        //购物车支付
                        $id=I('post.id');//商品id
                        $address=M('member_address')->field('Id')->Where("memberid=$memberid")->order('Id desc')->find();
                        $goods=M('commodity')->Where("Id=$id")->find();
                        if($id){
                                $code=$this->createNonceStr()."$memberid";
                                $map=array(
                                        'memberid'=>$memberid,
                                        'code'=>$code,
                                        'createtime'=>date("Y-m-d H:i:s"),
                                        'useprice'=>$goods['tradePrice'],
                                        'addressid'=>$address['Id'],
                                );
                                $bool=M('order')->add($map);//添加订单
                                if($bool){
                                        $orderid=M('order')->field('Id')->Where("code=$code")->find();//查订单id
                                        $maps=array(
                                                'orderid'=>$orderid['Id'],
                                                'goods_id'=>$id,
                                                'goods_name'=>$goods['comdName'],
                                                'sp_num'=>'1',
                                                'sp_price'=>$goods['tradePrice'],
                                        );
                                        M('order_details')->add($maps);//添加订单详情
                                        $msg=array('flag'=>'1','orderid'=>$orderid['Id']);
                                }else{
                                        $msg=array('flag'=>'-1','error'=>'订单添加失败');
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

	public function confirmPay(){//确认支付页
		$float=I('post.op');
                $msg=array('error'=>'身份验证失败');
                $memberid=1;
                if($float=='fruits_api_confirmPay'){
                	$id=I('post.id');
                	if($id){
                		$order=M('order')->Where("Id=$id")->find();
                		$addressid=$order['addressid'];
                		$order['desc']=M('order_details')->Where("orderid=$id")->select();
                		$order['address']=M('address')->Where("Id=$addressid")->find();
                		$map['member_coupons.status']=1;
                		$map['member_coupons.memberid']=$memberid;
                		$list=M('member_coupons')
        		            ->field('member_coupons.status,member_coupons.lq_time,coupons.valid_time')
        		            ->join('coupons on member_coupons.coupons_id=coupons.id')
        		            ->Where($map)
        		            ->select();
        		        foreach ($list as $key => $value) {//自动修改优惠券状态
        		            $day1= date('Y-m-d');
        		            $day2=$value['lq_time'];
        		            $days=$this->getDays($day1,$day2);//获取时间天数差
        		            if($days>$value['valid_time']){//判断优惠券是否失效
        		                $id=$value['Id'];
        		                $save['status']=3;
        		                M('member_coupons')->Where("Id=$id")->delete();
        		            }
        		        }
        		        $map['coupons.less_price']=array('lt',$order['useprice']);//获取可用的优惠券
        		        $coupons=M('member_coupons')
        		            ->field('member_coupons.Id,member_coupons.lq_time,coupons.*')
        		            ->join('coupons on member_coupons.coupons_id=coupons.id')
        		            ->Where($map)
        		            ->select();
                        foreach ($coupons as $key => $value) {
                                $lq_time=$value['lq_time'];
                                $valid_time=$value['valid_time'];
                               $coupons[$key]['js_time']=date('Y-m-d',strtotime("$lq_time +$valid_time day"));
                        }
                        $order['coupons']=$coupons;
                		$this->ajaxReturn($order,'JSON');
                	}else{
                		$msg=array('error'=>'缺少参数');
                		$this->ajaxReturn($msg,'JSON');
                	}
                }else{
                	$this->ajaxReturn($msg,'JSON');
                }
	}

        public function recharge(){//会员充值
                $float=I('post.op');
                if($float=='fruits_api_recharge'){
                    $price=I('post.price');//充值价
                       
                }else{
                    $msg=array('error'=>'身份验证失败');        
                }
                $this->ajaxReturn($msg,'JSON');
        }

}