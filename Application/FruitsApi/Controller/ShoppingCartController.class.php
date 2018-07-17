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
        $memberid=1;
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_shoppingCart'){
            $map['shopping_cart.memberid']=$memberid;
            $cart=M('shopping_cart')->field('group_id,group_name')->Where($map)->group('group_id')->select();
            $i=0;
            foreach ($cart as $key => $value) {
                $maps['shopping_cart.group_id']=$value['group_id'];
                $cart=M('shopping_cart')
                ->field('commodity.Id,commodity.comdName,commodity.src,commodity.groupid,commodity.discount,shopping_cart.goods_num,shopping_cart.price')
                ->join('LEFT JOIN commodity on shopping_cart.goods_id=commodity.Id')
                ->Where($map)
                ->order('shopping_cart.createtime desc')
                ->select();
                $carts[$i]['storeName']=$value['group_name'];
                $carts[$i]['typeId']=$value['group_id'];
                $carts[$i]['goodsList']=$cart;
                $i++;
            }
            if($cart){
                $this->ajaxReturn($carts,'JSON');
            }else{
               $this->ajaxReturn(0,'JSON');
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
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_addCart'){
            $map['goods_id']=$goodid;
            $map['memberid']=$memberid;
            $bool=M('shopping_cart')->Where($map)->find();
            if($bool){
                $save['goods_num']=$num;
                $bools=M('shopping_cart')->Where($map)->save($save);
                if($bools){
                    $ts['msg']='添加成功';//添加成功
                    $ts['flag']='1';
                }else{
                    $ts['msg']='添加失败';//添加失败
                    $ts['flag']='-1';
                }
            }else{
                $map['commodity.Id']=$goodid;
                $cart=M('commodity')
                ->field('commodity.groupid,commodity.Id,commodity_class.GroupName')
                ->join('LEFT JOIN commodity_class on commodity.groupid=commodity_class.Id')
                ->Where($map)
                ->find();
                $data=array(
                    'memberid'=>$memberid,
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
                    $ts['flag']='1';
                }else{
                    $ts['msg']='添加失败';//添加失败
                    $ts['flag']='-1';
                }
            }
            $this->ajaxReturn($ts,'JSON');
        }  else{
            $this->ajaxReturn($msg,'JSON');
        }  
        
    }

    public function cartNum(){
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_cartNum'){
            $count=M('shopping_cart')->Where("memberid=$memberid")->count();
            $msg=array('count'=>$count);
        }else{
            $msg=array('error'=>'身份验证失败');
        }
        $this->ajaxReturn($msg,'JSON');
    }

    public function delCart(){
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_delCart'){
            $id=I('post.id');
            foreach ($id as $key => $value) {
                $bool=M('shopping_cart')->Where("goods_id=$value")->delete();
            }
            if($bool){
                $msg['msg']='成功';
                $msg['flag']='1';
            }else{
                $msg['msg']='失败';
                $msg['flag']='-1';
            }
        }else{
            $msg=array('error'=>'身份验证失败');
        }
        $this->ajaxReturn($msg,'JSON');
   }
   

}