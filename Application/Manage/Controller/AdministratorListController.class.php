<?php

namespace Manage\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8;');
date_default_timezone_set('prc');
class AdministratorListController extends Controller {
    public function index(){
    	$admin=M('administrator')->select();
    	$this->assign('admin',$admin);
        $this->display();
    }
    public function add_administrator(){
        $this->display();
    }

    //状态修改
    public function change(){
    	$id=I('post.id');
    	$aid=I('post.aid');
    	if($aid==2){
    		$data['status']=2;
	    }else{
	    	$data['status']=1;
	    }
	    $bool=M('administrator')->where(array('Id'=>$id))->save($data);
    	 
    }
    //删除
    public function del(){
    	$id=I('post.id');
    	$idAll=I('post.idAll');
    	if($idAll){
    		foreach ($idAll as $key => $id) {
    			$bool=M('administrator')->where(array('Id'=>$id))->delete();//批量删除 
    		}
    	}
    	if($id){
    		$bool=M('administrator')->where(array('Id'=>$id))->delete(); //单独删除
    	}
    	
    }
    //管理员添加
    public function add(){
    	$art=M('administrator');
    	$password=I('post.userpassword');
    	$newpassword2=I('post.newpassword2');
    	$name=I('post.user-name');
    	if(empty($name)){
    		echo "<script>alert('用户名不能为空');history.go(-1);</script>";
            die;
    	}
    	if(empty($password)){
    		echo "<script>alert('密码不能为空');history.go(-1);</script>";
            die;
    	}else if ($password != $newpassword2){
    		echo "<script>alert('两次密码不一致');history.go(-1);</script>";
            die;
    	}
    	if($name){
    		$name_cf=$art->where(array('name'=>$name))->find();
    		if($name_cf){
    			echo "<script>alert('用户名已注册');history.go(-1);</script>";
            	die;
    		}
    	}
    	$data=[
    		'password'=>md5($password),
    		'name'=>$name,
    		'status'=>1,
    		'createtime'=>date('y-m-d h:i:s',time()),
    	];
    	$bool=$art->add($data); 
	   	if($bool){
	    	$this->success('操作成功',__CONTROLLER__.'/index',1);
	    }else{
			$this->error('操作失败','',1);
	   	}        
    }
}