<?php
namespace Manage\Controller;
use Think\Controller;
class MenuListController extends Controller {
    public function index(){
    	$menu=M('menu')->select();
    	$this->assign('menu',$menu);
    	$this->display();
    }
    //编辑
    public function edit_menu(){
    	$id=I('get.menu_id');
    	if($id){
    		$menu=M('menu')->Where("Id=$id")->find();
    		$this->assign('menu',$menu);
    	}
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
	    $bool=M('menu')->where(array('Id'=>$id))->save($data);
    	 
    }
    //删除
    public function del(){
    	$id=I('post.id');
    	$idAll=I('post.idAll');
    	if($idAll){
    		foreach ($idAll as $key => $id) {
    			$bool=M('menu')->where(array('Id'=>$id))->delete();//批量删除 
    		}
    	}
    	if($id){
    		$bool=M('menu')->where(array('Id'=>$id))->delete(); //单独删除
    	}
    	
    }
    //添加
    public function add(){
    	$name=I('post.name');
    	$content=I('post.content');
    	date_default_timezone_set('PRC');
		$time=date('y-m-d h:i:s',time());
		$menu_cl=I('post.menu_cl');
		
		$data=array(
			'name'=>$name,
			'desc'=>$content,
			'createtime'=>$time,
			'menu_cl'=>$menu_cl,
			'status'=>1,//默认开启
		);    	
		$bool=M('menu')->add($data);
		if($bool){

		    $this->success('操作成功',__CONTROLLER__.'/index',1);

		}else{

		    $this->error('操作失败','',1);

		}

    }
	//菜单修改
    public function change_menu(){
    	$name=I('post.name');
    	$content=I('post.content');
		$menu_cl=I('post.menu_cl');
		$id=I('post.edit_id');
		$data=array(
			'name'=>$name,
			'desc'=>$content,
			'menu_cl'=>$menu_cl,
		);    	
		$bool=M('menu')->Where("Id=$id")->save($data);
		if($bool){

		    $this->success('操作成功',__CONTROLLER__.'/index',1);

		}else{

		    $this->error('操作失败','',1);

		}

    }
}