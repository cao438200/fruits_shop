<?php

namespace Manage\Controller;

use Think\Controller;

class PaylistController extends Controller {

    public function index(){


      //实例化产品类别表
      $tab=M('bigad3')->where(array('dd'=>1))->select();
      $this->assign('type',$tab);




    	//分页

    	  $count      = M('pay')->where(array('dd'=>1))->count();// 查询满足要求的总记录数

        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list = M('pay')->where(array('dd'=>1))->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

          //引入广告类别表
      foreach ($list as $k => $v) {
       $tab=M('bigad3')->where(array('id'=>$v['aid']))->find();
       $list[$k]['type']=$tab['type'];
      }



        $this->assign('list',$list);// 赋值数据集

        $this->assign('pay',$list);

        $this->assign('page',$show);// 赋值分页输出




        

        //输出

        $this->display();

    }



      //图集基本信息添加页
    public function add(){
      //实例化产品类别表
      $tab=M('bigad3')->where(array('dd'=>1))->select();
      $this->assign('type',$tab);
      //var_dump($tab);
      $this->display();
    }



    //添加支付页

    public function article(){

    	$art=M('pay');

    	$title=I('post.title');

      $aid=I('post.type');

    	$link=I('post.link');





      //二维码上传

      $photo=isset($_FILES['img'])?$_FILES['img']:'';

      $upload = new \Think\Upload();// 实例化上传类    

      $upload->maxSize   = 10*1024*1024 ;// 设置附件上传大小    

      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

      $upload->rootPath  =      'Public/Uploads/'; // 设置附件上传目录    // 上传单个文件     

      $info   =   $upload->uploadOne($photo); 
      //获取上传文件信息     
      

      //小广告上传

      $photo2=isset($_FILES['img2'])?$_FILES['img2']:'';

      $upload = new \Think\Upload();// 实例化上传类    

      $upload->maxSize   = 10*1024*1024 ;// 设置附件上传大小    

      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

      $upload->rootPath  =      'Public/Uploads/'; // 设置附件上传目录    // 上传单个文件     

      $info2   =   $upload->uploadOne($photo2); 
      //获取上传文件信息  



      $data['title']=$title;

      $data['img']=$info['savepath'].$info['savename'];

      $data['img2']=$info2['savepath'].$info2['savename'];

      $data['link']=$link;

      $data['aid']=$aid;

      $bool=$art->add($data); 

      if($bool){

      	$this->success('操作成功',__CONTROLLER__.'/index',1);

      }else{

      	$this->error('操作失败','',1);

      }

    }





    //文章修改页

    public function show(){
        //实例化产品类别表
        $tab=M('bigad3')->where(array('dd'=>1))->select();
        $this->assign('type',$tab);

        $id=I('get.id');

        $pay=M('pay')->where(array('id'=>$id))->find();

        //var_dump($art);

        $this->assign('pay',$pay);

        $this->assign('id',$id);

    	  $this->display();

    }



      public function del($id){

            $art=M("pay");

            $art->where("id=$id")->setField('dd',0);

            $this->success("操作成功啦！",U('Paylist/index'));    

        }



    //修改文章方法

    public function change(){

    	$id=I('post.id');

    	$title=I('post.title');

    	$aid=I('post.type');

    	$link=I('post.link');

      $tab=M('pay')->where(array('id'=>$id))->find();

      $tupian=$tab['img'];

      $tupian2=$tab['img2'];







      //二维码修改

      $upload = new \Think\Upload();// 实例化上传类    

      $upload->maxSize   = 10*1024*1024 ;// 设置附件上传大小    

      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

      $upload->rootPath  =      'Public/Uploads/'; // 设置附件上传目录    // 上传单个文件     

      $info   =   $upload->uploadOne($_FILES['img']);    

     

     $pic.=$info['savepath'].$info['savename'];

     if(empty($pic)){     

       $path=$tupian;

     }else{

       $path=$pic;

     }

     //广告图修改

      $upload = new \Think\Upload();// 实例化上传类    

      $upload->maxSize   = 10*1024*1024 ;// 设置附件上传大小    

      $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

      $upload->rootPath  =      'Public/Uploads/'; // 设置附件上传目录    // 上传单个文件     

      $info2   =   $upload->uploadOne($_FILES['img2']);    

     

     $pic2.=$info2['savepath'].$info2['savename'];

     if(empty($pic2)){     

       $path2=$tupian2;

     }else{

       $path2=$pic2;

     }



      $data['title']=$title;

      $data['type']=$type;

      $data['link']=$link;

      $data['img']=$path;

      $data['img2']=$path2;

      $data['aid']=$aid;


      $bool=M('pay')->where(array('id'=>$id))->save($data); 

      if($bool){

      	$this->success('修改成功',__CONTROLLER__.'/index',1);

      }else{

      	$this->success('修改成功',__CONTROLLER__.'/index',1);

      }
   }

    public function bad(){
    $id=$_GET['id'];


    $bool=M('pay')->where(array('id'=>$id))->setField('ad',$_GET['why']);

    if($bool!==false){
      echo "<script>alert('修改成功');history.go(-1);</script>";
    }else{
      echo "<script>alert('修改失败');history.go(-1);</script>";
    }
    
   }



       public function select1(){

      $aid=I('get.id');
      //实例化产品类别表
      $tab=M('bigad3')->where(array('dd'=>1))->select();
      $this->assign('type',$tab);




      //分页

        $count      = M('pay')->where(array('dd'=>1,'aid'=>$aid))->count();// 查询满足要求的总记录数

        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list = M('pay')->where(array('dd'=>1,'aid'=>$aid))->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

          //引入广告类别表
      foreach ($list as $k => $v) {
       $tab=M('bigad3')->where(array('id'=>$v['aid']))->find();
       $list[$k]['type']=$tab['type'];
      }



        $this->assign('list',$list);// 赋值数据集

        $this->assign('pay',$list);

        $this->assign('page',$show);// 赋值分页输出




        

        //输出

        $this->display();

    }


           public function select2(){

      $aid=I('get.id');
      //实例化产品类别表
      $tab=M('bigad3')->where(array('dd'=>1))->select();
      $this->assign('type',$tab);




      //分页

        $count      = M('pay')->where(array('dd'=>1,'aid'=>$aid))->count();// 查询满足要求的总记录数

        $Page       = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性

        $list = M('pay')->where(array('dd'=>1,'aid'=>$aid))->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

          //引入广告类别表
      foreach ($list as $k => $v) {
       $tab=M('bigad3')->where(array('id'=>$v['aid']))->find();
       $list[$k]['type']=$tab['type'];
      }



        $this->assign('list',$list);// 赋值数据集

        $this->assign('pay',$list);

        $this->assign('page',$show);// 赋值分页输出




        

        //输出

        $this->display();

    }





}