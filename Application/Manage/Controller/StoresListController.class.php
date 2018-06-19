<?php
	namespace Manage\Controller;
	use Think\Controller;
	class StoresListController extends Controller {
		public function index(){
			$search=I('post.search');	
			if($search){
				$name=I('post.name');
				$this->assign('name',$name);
				$map['name']=array('like',"%$name%");
				$count=M('store')->where($map)->count();
				$p = getpage($count,10);
				$this->assign('page',$p->show());
				$store = M('store')->where($map)->order('store_key')->limit($p->firstRow.','.$p->listRows)->select();
				// $store=M('store')->Where($map)->select();
			}else{
				$login=array(
		            'sUserID'=>'admin',
		            'sPassword'=>'123456',
		            'sExportType'=>'JSON',
		            'sCharsetName'=>'UTF-8',
		        );
				$result=$this->get_port('http://211.149.155.236:90/order.asmx/ABC_Login',$login);//接口登陆返回值
				var_dump($result);
				if($result['Status']==1){//登陆成功
					$url='http://211.149.155.236:90/order.asmx/Get_ShpList';
					$data=array(
						'sUserEntry'=>1,
					);
					$shoplist=$this->get_port($url);//获取门店列表
					var_dump($shoplist);
				}else{
					$ts=$result['Description'];
					echo "<script>alert('$ts')</script>";
				}
				// if($result['Status']==1){
				// 	foreach ($contents as $key => $value) {
				// 		$value=get_object_vars($value);//strobj 转数组
				// 		$store_key=$value['系统编码'];
				// 		$data=array(
				// 			'store_key'=>$store_key,
				// 			'store_type'=>$value['店铺类型'],
				// 			'run_type'=>$value['经营类型'],
				// 			'area'=>$value['区域'],
				// 			'province'=>$value['省份'],
				// 			'city'=>$value['城市'],
				// 			'store_num'=>$value['店号'],
				// 			'name'=>$value['店名'],
				// 			'telephone'=>$value['电话'],
				// 			'site'=>$value['地址'],
				// 			'longitude'=>$value['X坐标'],
				// 			'latitude'=>$value['Y坐标'],

				// 		);
				// 		$map['store_key']=$store_key;
				// 		$bool=M('store')->Where($map)->select();//查看店铺是否存在
				// 		if(!$bool){//店铺不存在就添加
				// 			$add=M('store')->add($data);
				// 		}
				// 	}
				// }else{
				// 	$ts=$result['Description'];
				// 	echo "<script>alert('$ts')</script>";
				// }
				
				$count=M('store')->count();
				$p = getpage($count,10);
				$this->assign('page',$p->show());
				$store = M('store')->order('store_key')->limit($p->firstRow.','.$p->listRows)->select();
				// $store=M('store')->order('store_key')->select();
			}	
    		$this->assign('store',$store);
			$this->display();
		}
		public function add_store(){
			$id=I('get.store_id');
			$stores=M('store')->field('Id,name,site,longitude,latitude')->Where("Id=$id")->find();
			$this->assign('stores',$stores);
			$this->display();
		}
		
		public function edit()
		{
			$id=I('post.id');
			if($id){
				$data=array(
					'longitude'=>I('post.store_longitude'),
					'latitude'=>I('post.store_latitude'),
				);
				$bool=M('store')->Where("Id=$id")->save($data);
				if($bool){
			    	$this->success('操作成功',__CONTROLLER__.'/index',1);
			    }else{
					$this->error('操作失败','',1);
			   	}        
			}

		}
	}

