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
class ShoppingCartController extends Controller {

    public function shoppingCart(){//购物车接口
    	$float=I('post.op');
        $classid=I('post.id');//分类id
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_shoppingCart'){
            $map['shopping_cart.memberid']=1;
            $cart=M('shopping_cart')->field('group_id,group_name')->Where($map)->group('group_id')->select();
            foreach ($cart as $key => $value) {
                $maps['shopping_cart.group_id']=$value['group_id'];
                $cart=M('shopping_cart')
                ->field('commodity.Id,commodity.comdName,commodity.img_src,commodity.groupid,shopping_cart.goods_num,shopping_cart.price')
                ->join('LEFT JOIN commodity on shopping_cart.goods_id=commodity.Id')
                ->Where($map)
                ->order('shopping_cart.createtime desc')
                ->select();
                $carts[$value['group_name']]=$cart;
            }
            if($cart){
                $this->ajaxReturn($carts,'JSON');
            }else{
                $ts['data']=0;
                $ts['msg']='购物车为空'；
                $this->ajaxReturn($ts,'JSON');
            }	 	
    	}  else{
    		$this->ajaxReturn($msg,'JSON');
    	}  
        
    }

    public function addCart(){//添加购物车接口
        $float=I('post.op');
        $goodid=I('post.id');//商品id
        $num=I('post.num');//商品个数
        $price=I('post.price');//总价
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_addCart'){
            $map['commodity.Id']=$goodid;
            $cart=M('commodity')
            ->field('commodity.groupid,commodity.Id,commodity_class.GroupName')
            ->join('LEFT JOIN commodity_class on commodity.groupid=commodity_class.Id')
            ->Where($map)
            ->find();
            $data=array(
                'memberid'=>1,
                'goods_id'=>$cart['Id'],
                'goods_num'=>$num,
                'group_name'=>$cart['GroupName'],
                'price'=>$price,
                'createtime'=>date("Y-m-d H:i:s"),
                'group_id'=>$cart['groupid'],
            );
            $bool=M('shopping_cart')->add($data);
            if($bool){
                $ts['msg']='添加成功';//添加成功
                $ts['data']='1';
            }else{
                $ts['msg']='添加失败';//添加失败
                $ts['data']='-1';
            }
            $this->ajaxReturn($ts,'JSON');
        }  else{
            $this->ajaxReturn($msg,'JSON');
        }  
        
    }

}