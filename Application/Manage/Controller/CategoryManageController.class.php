<?php
	namespace Manage\Controller;
	use Think\Controller;
	class CategoryManageController extends Controller {
		public function index(){
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
			$list=M('commodity_class')->select();
			$this->assign('list',$list);
			$this->display();
		}
	}


















