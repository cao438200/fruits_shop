<?php
	namespace Manage\Controller;
	use Think\Controller;
	class ProductsController extends Controller {
		public function index(){
			$login=array(
            	'sUserID'=>'admin',
            	'sPassword'=>'123456',
            	'sExportType'=>'JSON',
            	'sCharsetName'=>'UTF-8',
        	);
			$result=$this->get_port('http://211.149.155.236:90/PDA.ASMX/ABC_Login',$login);//接口登陆返回值
			if($result['Status']==1){//登陆成功
				$url='211.149.155.236:90/PDA.ASMX/Get_ItemListEx';
				$data=array(
					'sValue'=>'A1',
					'sSearch'=>'线上',
					'sWhsEntry'=>'',
				);
				$list=$this->get_port($url,$data);//获取商品列表
				foreach ($list as $key => $value) {
					if($value['是否停用']=="正常"){
						$status=1;
					}else{
						$status=0;
					}
					$class=$value['商品分类'];
					$group=M('commodity_class')->field('Id')->Where("GroupName='$class'")->find();//查询分类id
					$brand=$value["品牌"];
					$brandid=M('commodity_brand')->field('Id')->Where("BrandName='$brand'")->find();//查询品牌id
					$data=array(
						'encode'=>$value["系统编码"],
						'comdNum'=>$value["商品编号"],
						'comdName'=>$value["商品名称"],
						'brand'=>$value["品牌"],
						'FcomdType'=>$value["商品分类"],
						'specModel'=>$value["规格型号"],
						'ScomdType'=>$value["二级分类"],
						'TcomdType'=>$value["三级分类"],
						'Type'=>$value["A1"],
						'unit'=>$value["计量单位"],
						'retailPrice'=>$value["零售价"],
						'tradePrice'=>$value["批发价"],
						'color'=>$value["颜色"],
						'status'=>$status,
						'groupid'=>$group['Id'],
						'brandid'=>$brandid['Id'],
					);
					$map['encode']=$value['系统编码'];
					$bool=M('commodity')->Where($map)->select();
					if(!$bool){
						M('commodity')->add($data);
					}
				}
			}else{
				$ts=$result['Description'];
				echo "<script>alert('$ts')</script>";
			}
			$op=I('post.search');
			if($op){
				$name=I('post.name');
				$this->assign('name',$name);
				$search['comdName']=array('like',"%$name%");
			}
			$count=M('commodity')->where($search)->count();
			$p = getpage($count,9);
			$this->assign('page',$p->show());
			$list = M('commodity')->where($search)->order('encode desc')->limit($p->firstRow.','.$p->listRows)->select();
			$this->assign('list',$list);
			$this->display();
		}

		public function edit_products(){
			$id=I('get.products_id');
			$products=M('commodity')->Where("Id=$id")->find();
			$name=$products['comdName'];//商品名
			$menuid=$products['menuid'];//商品菜单id
			$menuid=json_decode($menuid);//转化为数组
			foreach($menuid as $key=>$value){
				$menuids[$value]=$value;
			}
			$menu=M('menu')->field('Id,name,menu_main')->Where('status=1')->select();//所有菜单
			foreach ($menu as $key => $value) {
				$flag=stristr($name,$value['menu_main']);//判断主料是否在商品名中
				if($flag){
					$menus[$key]['Id']=$value['Id'];
					$menus[$key]['name']=$value['name'];
					if($menuids[$value['Id']]){//判断是否已选
						$menus[$key]['flag']=1;
					}else{
						$menus[$key]['flag']=0;
					}
				}
			}
			$this->assign('menus',$menus);
			$this->assign('products',$products);
			$this->display();
		}

		public function change(){
			$id=I('post.edit_id');
			$data['desc']=I('post.content');//商品详情
			$menuid=I('post.menu');
			$menuid=$data['menuid']=json_encode($menuid);
            $photo=isset($_FILES['img'])?$_FILES['img']:'';

            $upload = new \Think\Upload();// 实例化上传类    

            $upload->maxSize   = 10*1024*1024 ;// 设置附件上传大小    

            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型

            $upload->rootPath  =      'Public/Uploads/'; // 设置附件上传目录    // 上传单个文件     

            $info   =   $upload->uploadOne($photo);    // 上传错误提示错误信息  

            if(!$info) {      

                $this->error($upload->getError());

            }else{// 上传成功 获取上传文件信息     

                $data['img_src']=$info['savepath'].$info['savename'];
                $bool=M('commodity')->Where("Id=$id")->save($data); 
                if($bool){
                    $this->success('操作成功',__CONTROLLER__.'/index',1);
                }else{

                    $this->error('操作失败','',1);

                }

            }

		}
	}