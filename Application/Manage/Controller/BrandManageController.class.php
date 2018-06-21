<?php
	namespace Manage\Controller;
	use Think\Controller;
	class BrandManageController extends Controller {
		public function index(){
			$result=$this->get_logion_status();//接口登陆返回值
			if($result['Status']==1){//登陆成功
				$url='http://211.149.155.236:90/order.asmx/Get_DicListByItem';
				$data=array(
					'sDicKey'=>'BrandList',
				);
				$BrandList=$this->get_port($url,$data);//获取品牌列表
				foreach ($BrandList as $key => $value) {
					$map['DocEntry']=$value['DocEntry'];
					$bool=M('commodity_brand')->Where($map)->select();
					if(!$bool){
						M('commodity_brand')->add($value);
					}
				}
			}else{
				$ts=$result['Description'];
				echo "<script>alert('$ts')</script>";
			}
			
			$list=M('commodity_brand')->select();
			$this->assign('list',$list);
			$this->display();
		}
	}