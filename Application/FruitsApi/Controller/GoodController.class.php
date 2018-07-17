<?php
namespace FruitsApi\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:*');  
// 响应头设置  
header('Access-Control-Allow-Headers:x-requested-with,content-type');   
class GoodController extends Controller {

    public function goodDetails(){//商品详情
        $float=I('post.op');
        $goodid=I('post.id');//商品id
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_goodDetails'){
            $map['Id']=$goodid;
            $data=M('commodity')->field('comdName,retailPrice,tradePrice,src,desc,menuid,discount,rank,reservoir')->Where($map)->find();
            $this->ajaxReturn($data,'JSON');
        }  else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }
    
    public function goodMenu(){//商品菜单
        $float=I('post.op');
        $goodid=I('post.id');//商品id
        if($float=='fruits_api_goodMenu'){
            $map['Id']=$goodid;
            $menuid=M('commodity')->field('menuid')->Where($map)->find();
            $menuid=json_decode($menuid['menuid']);
            if($menuid){
                for ($i=0; $i <2 ; $i++) {//只获取商品两个菜单
                    $menu[$i]=M('menu')->field('Id,name,menu_main,first_minor,se_minor')->Where("Id=$menuid[$i]")->find();
                } 
                $this->ajaxReturn($menu,'JSON');
            }else{
                $msg=0;
            }
        }else{
            $msg=array('error'=>'身份验证失败');
        }
        $this->ajaxReturn($msg,'JSON');  
    }

    public function menu(){
        $float=I('post.op');
        $menuid=I('post.id');//菜单id
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_menu'){
            if($menuid){
                $map['Id']=$menuid;
                $data=M('menu')->field('desc,Id')->Where($map)->find();
                $this->ajaxReturn($data,'JSON');
            }else{
                $msg=array('data'=>-1,'msg'=>'没有菜单id');
                $this->ajaxReturn($msg,'JSON');
            } 
        }else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

    public function addGood(){//新增商品
        $float=I('post.op');
        if($float=='fruits_api_addGood'){
            $sysNum=I('post.sysNum');
            $comdNum=I('post.comdNum');
            $comdName=I('post.comdName');
            $colour=I('post.colour');//非必传
            $brand=I('post.brand');
            $FcomdType=I('post.FcomdType');
            $specModel=I('post.specModel');
            $ScomdType=I('post.ScomdType');
            $TcomdType=I('post.TcomdType');
            $Type=I('post.Type');
            $unit=I('post.unit');
            $retailPrice=I('post.retailPrice');
            $tradePrice=I('post.tradePrice');
            $status=I('post.status');
            if($sysNum && $comdNum && $comdName && $brand && $FcomdType && $specModel && $ScomdType && $TcomdType && $Type && $unit && $retailPrice && $tradePrice && $status){
                $data=array(
                    'sysNum'=>$sysNum,
                    'comdNum'=>$comdNum,
                    'comdName'=>$comdName,
                    'colour'=>$colour,
                    'brand'=>$brand,
                    'FcomdType'=>$FcomdType,
                    'specModel'=>$specModel,
                    'ScomdType'=>$ScomdType,
                    'TcomdType'=>$TcomdType,
                    'Type'=>$Type,
                    'unit'=>$unit,
                    'retailPrice'=>$retailPrice,
                    'tradePrice'=>$tradePrice,
                    'status'=>$status,
                );
                $bool=M('commodity')->add($map);
                if($bool){
                    $msg=array('data'=>'0','msg'=>'新增失败');
                }else{
                    $msg=array('data'=>'1','msg'=>'新增成功');
                }
            }else{
                $msg=array('data'=>'0','msg'=>'缺少参数新增失败');
            }
        }else{
            $msg=array('data'=>'-1','msg'=>'验证失败');
        }
        $this->ajaxReturn($msg,'JSON');  
    }

    public function reviseGood(){//修改商品
        $float=I('post.op');
        if($float=='fruits_api_addGood'){
            $sysNum=I('post.sysNum');
            $comdNum=I('post.comdNum');
            $comdName=I('post.comdName');
            $colour=I('post.colour');//非必传
            $brand=I('post.brand');
            $FcomdType=I('post.FcomdType');
            $specModel=I('post.specModel');
            $ScomdType=I('post.ScomdType');
            $TcomdType=I('post.TcomdType');
            $Type=I('post.Type');
            $unit=I('post.unit');
            $retailPrice=I('post.retailPrice');
            $tradePrice=I('post.tradePrice');
            $status=I('post.status');
            if($sysNum && $comdNum && $comdName && $brand && $FcomdType && $specModel && $ScomdType && $TcomdType && $Type && $unit && $retailPrice && $tradePrice && $status){
                $data=array(
                    'comdNum'=>$comdNum,
                    'comdName'=>$comdName,
                    'colour'=>$colour,
                    'brand'=>$brand,
                    'FcomdType'=>$FcomdType,
                    'specModel'=>$specModel,
                    'ScomdType'=>$ScomdType,
                    'TcomdType'=>$TcomdType,
                    'Type'=>$Type,
                    'unit'=>$unit,
                    'retailPrice'=>$retailPrice,
                    'tradePrice'=>$tradePrice,
                    'status'=>$status,
                );
                $bool=M('commodity')->Where("sysNum='$sysNum'")->save($map);
                if($bool){
                    $msg=array('data'=>'0','msg'=>'新增失败');
                }else{
                    $msg=array('data'=>'1','msg'=>'新增成功');
                }
            }else{
                $msg=array('data'=>'0','msg'=>'缺少参数新增失败');
            }
        }else{
            $msg=array('data'=>'-1','msg'=>'验证失败');
        }
        $this->ajaxReturn($msg,'JSON');  
    }





}