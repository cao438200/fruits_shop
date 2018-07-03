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
		$type=I('post.type');//判断是购物车支付还是订单支付1.购物车2.订单
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_pay'){
        	$id=I('post.id');//订单id
        	$type=I('post.type');//订单类型
        	$appointment_time=I('post.appointment_time');//预约时间
        	$price=I('post.price');
        	if($id && $type && $appointment_time && $price){

        		//微信支付待开发啊

        	}else{
        		$msg=array('error'=>'缺少参数');
        		$this->ajaxReturn($msg,'JSON');
        	}
        }else{
        	$this->ajaxReturn($msg,'JSON');
        }
	}


	public function cartPay(){//购物车点击支付
		$float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_cartPay'){
        	//购物车支付
        	$id=I('post.id')//购物车id
        	$yh_price=I('post.yh_price');//优惠价
        	$zf_price=I('post.zf_price');//支付价
        	$address=M('member_address')->field('Id')->Where("memberid=$memberid")->order('Id')->find();
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
        			$this->ajaxReturn($orderid,'JSON');//返回订单id
        		}else{
        			$msg=array('error'=>'订单添加失败');
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

	public function confirmPay(){//确认支付页
		$float=I('post.op');
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_confirmPay'){
        	$id=I('post.id');
        	if($id){
        		$order=M('order')->Where("Id=$id")->find();
        		$order['desc']=M('order_details')->Where("orderid=$id")->select();
        		$order['address']=M('address')->Where("Id=$order['addressid']")->find();
        		$this->ajaxReturn($order,'JSON');
        	}else{
        		$msg=array('error'=>'缺少参数');
        		$this->ajaxReturn($msg,'JSON');
        	}
        }else{
        	$this->ajaxReturn($msg,'JSON');
        }
	}

	public function overtimeOrder(){//订单超时
		$float=I('post.op');
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_overtimeOrder'){
        	$id=I('post.id');
        	if($id){
        		$order=M('order')->Where("Id=$id")->save('status=5');//修改订单状态
        		if($order){
        			$msg=array('data'=>'1','msg'=>'成功');
        		}else{
        			$msg=array('data'=>'-1','msg'=>'失败');
        			
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

}