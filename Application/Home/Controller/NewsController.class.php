<?php
namespace Home\Controller;
use Think\Controller;
class NewsController extends Controller {
    public function index(){
        //网站优化
        $website=M('lianxi')->where(array('id'=>2))->find();
        $this->assign('website',$website);

        $links=M('alink')->where(array('dd'=>1))->order('id desc')->select();//友情链接

        $this->assign('links',$links);
        //分类
        $tab=M('bigad2')->where(array('dd'=>1))->order('id asc')->select();
        $this->assign('types',$tab);
        $id=I('get.id');
        $type=I('get.type');
        //分页
        if($type){
            $count = M('article')->where(array('dd'=>1,'aid'=>$type))->count();// 查询满足要求的总记录数
        }else{
            $count = M('article')->where(array('dd'=>1))->count();// 查询满足要求的总记录数
        }
            $pagesize =5;//每页显示条目
            $page = floor($count/$pagesize);// 总页数
            $pa= I('get.pa');
            if(empty($pa)){
               $pa=0; 
            }
            if($pa<0){
                $pa=0;
            }
            if($page<$pa){
                $pa=$page;
            }
            
            $this->assign('pagesize',$pagesize);
            $this->assign('page',$page);
            $this->assign('pa',$pa);
            // $show       = $Page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            if($pa==$page){
                if($count%$pagesize !=0){
                    $psize=$count-$pagesize*$page;
                }else{
                    $pa=$page-1;
                    $psize=$pagesize;
                }
                if($type){
                    $articles = M('article')->where(array('dd'=>1,'aid'=>$type))->order('id desc')->limit($pa*$pagesize,$psize)->select();
                }else{
                    $articles = M('article')->where(array('dd'=>1))->order('id desc')->limit($pa*$pagesize,$psize)->select();
                }
               
            }else{
                if($type){
                    $articles = M('article')->where(array('dd'=>1,'aid'=>$type))->order('id desc')->limit($pa*$pagesize,$pagesize)->select();
                }else{
                   $articles = M('article')->where(array('dd'=>1))->order('id desc')->limit($pa*$pagesize,$pagesize)->select();
                }
                 
            }
        if($id){
            $art=M('article')->where(array('id'=>$id))->order('id desc')->find();
            $art1=M('article')->where(array('id'=>$id+1))->order('id desc')->find();
            $art2=M('article')->where(array('id'=>$id-1))->order('id desc')->find();
            $type=$art['aid'];
        }             
        $art['content']=htmlspecialchars_decode($art['content']);

        $title=M('bigad2')->where(array('dd'=>1,'id'=>$type))->order('id asc')->find();
        $this->assign('title',$title);

        //var_dump($art);
        $this->assign('articles',$articles);

        $this->assign('art1',$art1);

        $this->assign('art2',$art2);

        $this->assign('art',$art);

        $this->assign('type',$type);
        $this->assign('id',$id);
        //输出
        $this->display();
    }
}