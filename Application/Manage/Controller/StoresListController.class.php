<?php
	namespace Manage\Controller;
	use Think\Controller;
	class StoresListController extends Controller {
		public function index(){
			$url = "http://124.225.146.25:3003/crm.asmx/Get_ShpList";
			$ch  = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //返回数据不直接输出
    		curl_setopt($curl, CURLOPT_POST, 1);
    		curl_setopt($curl, CURLOPT_POSTFIELDS);
    		$content = curl_exec($ch);                    //执行并存储结果
    		$contents=json_decode($content);
    		// print_r($contents);
			 // print_r(get_object_vars($contents[0]));
			foreach ($contents as $key => $value) {
				$value=get_object_vars($value);//strobj 转数组
				$store_key=$value['系统编码'];
				$data=array(
					'store_key'=>$store_key,
					'store_type'=>$value['店铺类型'],
					'run_type'=>$value['经营类型'],
					'area'=>$value['区域'],
					'province'=>$value['省份'],
					'city'=>$value['城市'],
					'store_num'=>$value['店号'],
					'name'=>$value['店名'],
					'telephone'=>$value['电话'],
					'site'=>$value['地址'],
					'longitude'=>$value['X坐标'],
					'latitude'=>$value['Y坐标'],

				);
				$map['store_key']=$store_key;
				$bool=M('store')->Where($map)->select();//查看店铺是否存在
				if(!$bool){//店铺不存在就添加
					$add=M('store')->add($data);
				}
			}
			$store=M('store')->order('store_key')->select();
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

