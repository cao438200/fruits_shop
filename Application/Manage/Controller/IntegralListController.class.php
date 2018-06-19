<?php
namespace Manage\Controller;
use Think\Controller;
class IntegralListController extends Controller {
    public function index(){
    	$integral=M('integral')->select();
    	$this->assign('integral',$integral);

    	$this->display();
    }

    public function edit(){
    	$id=I('get.jf_id');
    	if($id){
    		$integral=M('integral')->Where("Id=$id")->find();
    		$this->assign('integral',$integral);
    	}
    	$this->display();
    }


    public function add(){
    	$id=I('post.jf_id');
    	$data['multiple']=I('post.multiple');
    	$bool=M('integral')->Where("Id=$id")->save($data);
    	if($bool){
    		$this->success('操作成功',__CONTROLLER__.'/index',1);
    	}else{
            $this->error('操作失败','',1);
        }
    }

}