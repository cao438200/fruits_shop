<?php

	namespace Manage\Controller;
	use Think\Controller;
	/**
	* 
	*/
	class MemberRechargeController extends Controller
	{
		public function index()
		{
			$M=M('pay_rank');
			$member_name=I('post.member_name');//搜索用户名
			$this->assign('member_name',$member_name);
			$start_time=I('post.start_time');//开始时间
			$this->assign('start_time',$start_time);
			if($start_time){
				$start=$start_time;
			}else{
				$start='2000-11-11';
			}
    		$end_time=I('post.end_time');//结束时间
    		$this->assign('end_time',$end_time);
    		if($end_time){
				$end=$end_time;
			}else{
				$end='3000-11-11';
			}
    		$sosou=I('post.op');//判断是否搜索
			if($sosou){
				if($member_name){
		    		$map['b.sVIPName']=array('like',"%$member_name%");
		    		$map['a.paytime'] = array('between',"$start,$end 23:59:59");
		    	}else{
		    		$map['a.paytime'] = array('between',"$start,$end 23:59:59");
		    	}
		    	$count=$M->alias('a')->field('a.pay_num,a.give_num,a.remark,a.paytime,a.type,a.status,b.sVIPName,b.sCardID')->join('member b on a.member_card=b.sCardID')->Where($map)->count();
		    	$p = getpage($count,1);//分页
		    	$this->assign('page',$p->show());
		    	$member_pay=$M->alias('a')->field('a.pay_num,a.give_num,a.remark,a.paytime,a.type,a.status,b.sVIPName,b.sCardID')->join('member b on a.member_card=b.sCardID')->Where($map)->order('paytime')->limit($p->firstRow.','.$p->listRows)->select();
			}else{
				$count = $M->count();//总条数
	        	$p = getpage($count,1);//分页
	        	$this->assign('page',$p->show());
	        	// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
				$member_pay=$M->alias('a')->field('a.pay_num,a.give_num,a.remark,a.paytime,a.type,a.status,b.sVIPName,b.sCardID')->join('member b on a.member_card=b.sCardID')->order('paytime')->limit($p->firstRow.','.$p->listRows)->select();
			}
			$this->assign('member_pay',$member_pay);
			$this->display();
		}
	}