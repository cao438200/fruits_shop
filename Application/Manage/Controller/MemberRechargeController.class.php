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
			$start=I('post.start_time');//开始时间
			$this->assign('start_time',$start);
    		$end=I('post.end_time');//结束时间
    		$this->assign('end_time',$end);
    		$sosou=I('post.op');//判断是否搜索
			if($sosou){
				if($member_name){
		    		if($start && $end){
		    			$map['b.sVIPName']=array('like','%$member_name%');
		    			$map['_string'] = "a.paytime between $start AND '$end 23:59:59' ";
		    		}else{
		    			$map['b.sVIPName']=array('like',"%$member_name%");
		    		}
		    	}elseif($start && $end){
		    		$map['_string'] = "a.paytime between $start AND '$end 23:59:59' ";
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