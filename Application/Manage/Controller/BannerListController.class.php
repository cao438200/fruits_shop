<?php
namespace Manage\Controller;
use Think\Controller;
class BannerListController extends Controller {
    public function index(){
    	$banner=M('banner')->order('sort desc')->select();
    	$this->assign('banner',$banner);

    	$this->display();
    }

    public function add()
    {
    	
    	$this->display();
    }

    //编辑轮播图
      public function edit()
    {
        $op=I('post.op');//判断是否是修改后的数据

        if($op){

            $id=I('post.banner_id');
            $data['sort']=I('post.px');
            $photo=isset($_FILES['img'])?$_FILES['img']:'';

            if($photo['name']){
                $upload = new \Think\Upload();// 实例化上传类    

                $upload->maxSize   = 10*1024*1024 ;// 设置附件上传大小    

                $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

                $upload->rootPath  =      'Public/Uploads/'; // 设置附件上传目录    // 上传单个文件     

                $info   =   $upload->uploadOne($photo);    // 上传错误提示错误信息  

                if(!$info) {      

                    $this->error($upload->getError());

                }else{// 上传成功 获取上传文件信息     

                    $data['src']=$info['savepath'].$info['savename'];
                    $data['url']='http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/'.$info['savepath'].$info['savename'];
                    
                }
            }
            $bool=M('banner')->Where("Id=$id")->save($data); 
            if($bool){
                $this->success('操作成功',__CONTROLLER__.'/index',1);
            }else{

                    $this->error('操作失败','',1);

            }
        }else{
            $id=I('get.id');
            $banner=M('banner')->Where("Id=$id")->find();
            $this->assign('banner',$banner);
            $this->display();
        }
        
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
	    $bool=M('banner')->where(array('Id'=>$id))->save($data);
    	 
    }
    //删除
    public function del(){
    	$id=I('post.id');
    	$idAll=I('post.idAll');
    	if($idAll){
    		foreach ($idAll as $key => $id) {
    			$bool=M('banner')->where(array('Id'=>$id))->delete();//批量删除 
    		}
    	}
    	if($id){
    		$bool=M('banner')->where(array('Id'=>$id))->delete(); //单独删除
    	}
    	
    }

    //添加轮播图
    public function add_banner(){

    	$art=M('banner');

    	$sort=I('post.px');
  		$data['sort']=$sort;
	      //图片上传

	      $photo=isset($_FILES['img'])?$_FILES['img']:'';

	      $upload = new \Think\Upload();// 实例化上传类    

	      $upload->maxSize   = 10*1024*1024 ;// 设置附件上传大小    

	      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

	      $upload->rootPath  =      'Public/Uploads/'; // 设置附件上传目录    // 上传单个文件     

	      $info   =   $upload->uploadOne($photo);    // 上传错误提示错误信息  

	      if(!$info) {      

	      	$this->error($upload->getError());

	      }else{// 上传成功 获取上传文件信息     

		      $data['src']=$info['savepath'].$info['savename'];
              $data['url']='http://'.$_SERVER['HTTP_HOST'].'/Public/Uploads/'.$info['savepath'].$info['savename'];

		      $data['createtime']=date('Y-m-d H:i:s');


		      $bool=$art->add($data); 

		      if($bool){

		      	$this->success('操作成功',__CONTROLLER__.'/index',1);

		      }else{

		      	$this->error('操作失败','',1);

		    }

    	}
    }

}