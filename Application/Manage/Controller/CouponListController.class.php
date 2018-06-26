<?php
namespace Manage\Controller;
use Think\Controller;
class CouponListController extends Controller {
    public function index(){
    	$coupons=M('coupons')->select();
    	$this->assign('coupons',$coupons);

    	$this->display();
    }

    public function edit(){
    	$id=I('get.coupon_id');
    	if($id){
    		$coupons=M('coupons')->Where("Id=$id")->find();
    		$this->assign('coupons',$coupons);
    	}
    	$this->display();
    }


    public function add(){
    	$id=I('post.coupon_id');
    	$data['less_price']=I('post.less_price');
        $data['reduce_price']=I('post.reduce_price');
        $data['valid_time']=I('post.valid_time');
        $data['start_time']=I('post.start_time');
        $data['end_time']=I('post.end_time');
        $data['desc']=I('post.desc');
    	$bool=M('coupons')->Where("Id=$id")->save($data);
    	if($bool){
    		$this->success('操作成功',__CONTROLLER__.'/index',1);
    	}else{
            $this->error('操作失败','',1);
        }
    }

}