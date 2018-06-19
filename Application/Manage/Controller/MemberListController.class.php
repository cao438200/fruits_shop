<?php
namespace Manage\Controller;
use Think\Controller;
class MemberListController extends Controller {
    public function index(){
    	$member_name=I('post.member_name');//搜索用户名
    	$start=I('post.start_time');//开始时间
    	$end=I('post.end_time');//结束时间
    	$sosou=I('post.op');//判断是否搜索
    	if($sosou){	
	    	if($member_name){
	    		if($start && $end){
	    			$map['sVIPName']=array('like','%$member_name%');
	    			$map['_string'] = "createtime between $start AND '$end 23:59:59' ";
	    		}else{
	    			$map['sVIPName']=array('like',"%$member_name%");
	    		}
	    	}elseif($start && $end){
	    		$map['_string'] = "createtime between $start AND '$end 23:59:59' ";
	    	}
	    	$count=M('member')->where($map)->count();
			$p = getpage($count,2);
			$this->assign('page',$p->show());
			$member = M('member')->where($map)->order('createtime desc')->limit($p->firstRow.','.$p->listRows)->select();

    	}else{
    		$count = M('member')->count();//总条数
	        $p = getpage($count,2);//分页
	        $this->assign('page',$p->show());
	        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	        $member = M('member')->order('createtime desc')->limit($p->firstRow.','.$p->listRows)->select();
	    		
    	}
    	$this->assign('name',$member_name);
    	// var_dump($member);
    	$this->assign('member',$member);
        $this->display();
    }

    //会员详情
    public function particulars()
    {
    	$id=I('get.id');
    	$member=M('member')->Where(array('Id',$id))->find();
    	$this->assign('member',$member);
    	$this->display();
    }
}