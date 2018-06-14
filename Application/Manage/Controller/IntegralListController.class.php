<?php
namespace Manage\Controller;
use Think\Controller;
class IntegralListController extends Controller {
    public function index(){
    	$integral=M('integral')->select();
    	$this->assign('integral',$integral);

    	$this->display();
    }

}