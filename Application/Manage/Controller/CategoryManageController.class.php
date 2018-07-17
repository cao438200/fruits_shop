<?php
	namespace Manage\Controller;
	use Think\Controller;
	class CategoryManageController extends Controller {
		public function index(){
			if(!$_SESSION['categoryapi']){
				$result=$this->get_logion_status();//接口登陆返回值
				if($result['Status']==1){//登陆成功
					$url='http://211.149.155.236:90/order.asmx/Get_DicListByItem';
					$data=array(
						'sDicKey'=>'GroupList',
					);
					$GroupList=$this->get_port($url,$data);//获取分类列表
					foreach ($GroupList as $key => $value) {
						$map['DocEntry']=$value['DocEntry'];
						$bool=M('commodity_class')->Where($map)->select();
						if(!$bool){
							M('commodity_class')->add($value);
						}
					}
				}else{
					$ts=$result['Description'];
					echo "<script>alert('$ts')</script>";
				}
				session_start();
                $_SESSION['categoryapi']=1;//储层获取商品品牌状态
			}
			$list=M('commodity_class')->select();
			$this->assign('list',$list);
			$this->display();
		}

		public function edit(){
			$op=I('post.op');
			if($op){
				$id=I('post.group_id');
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
				$bool=M('commodity_class')->Where("Id=$id")->save($data); 
		        if($bool){
		           	$this->success('操作成功',__CONTROLLER__.'/index',1);
		        }else{
		            $this->error('操作失败','',1);
		        }         
			}else{
				$id=I('get.id');
				$group=M('commodity_class')->Where("Id=$id")->find();
				$this->assign('group',$group);
				$this->display();
			}	
		}


	}


















