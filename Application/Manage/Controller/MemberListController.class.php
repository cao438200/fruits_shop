<?php
namespace Manage\Controller;
use Think\Controller;
class MemberListController extends Controller {
    public function index(){
    	$member_name=I('post.member_name');//搜索用户名
        $this->assign('member_name',$member_name);
        $start_time=I('post.start_time');//开始时间
        $this->assign('start_time',$start);
        if($start_time){
            $start=$start_time;
        }else{
            $start='2000-11-11';
        }
        $end_time=I('post.end_time');//结束时间
        $this->assign('end_time',$end);
        if($end_time){
            $end=$end_time;
        }else{
            $end='3000-11-11';
        }
    	$sosou=I('post.op');//判断是否搜索
    	if($sosou){	
	    	if($member_name){
	    		$map['sVIPName']=array('like', "%$member_name%");
	    		$map['createtime'] = array('between',"$start,$end 23:59:59");
	    	}else{
	    		$map['createtime'] = array('between',"$start,$end 23:59:59");
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