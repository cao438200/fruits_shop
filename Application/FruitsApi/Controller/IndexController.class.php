<?php
namespace FruitsApi\Controller;
use Think\Controller;
use Org\Util;
header("Content-type: text/html; charset=utf-8"); 
// 指定允许其他域名访问  
header('Access-Control-Allow-Origin:*');  
// 响应类型  
header('Access-Control-Allow-Methods:*');  
// 响应头设置  
header('Access-Control-Allow-Headers:x-requested-with,content-type');   
class IndexController extends Controller {
    public function goodsClass(){//商品分类接口
    	$float=I('post.op');
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_index'){
    		$goods_class=M('commodity_class')->select();
       	 	$this->ajaxReturn($goods_class,'JSON');
    	}  else{
    		$this->ajaxReturn($msg,'JSON');
    	}  
        
    }

    public function goods(){//首页商品接口
    	$float=I('post.op');
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_goods'){
    		$map['status']=1;
    		$goods=M('commodity')->field('Id,comdName,img_src,retailPrice,tradePrice')->Where($map)->order('encode desc')->limit(0,6)->select();
    		$this->ajaxReturn($goods,'JSON');
    	}else{
    		$this->ajaxReturn($msg,'JSON');
    	}
    }

    public function banner(){//首页banner图
    	$float=I('post.op');
    	$msg=array('error'=>'身份验证失败');
    	if($float=='fruits_api_banner'){
    		$banner=M('banner')->Where('status=1')->order('sort desc')->select();
    		$this->ajaxReturn($banner,'JSON');
    	}else{
    		$this->ajaxReturn($msg,'JSON');
    	}
    }


    public function getSingn(){//获取坐标参数接口
        $float=I('post.op');
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_getSingn'){
            $jsapiTicket = $this->get_jsapi_ticket();
            // 注意 URL 一定要动态获取，不能 hardcode.
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $timestamp = time();
            $nonceStr = $this->createNonceStr();
            // 这里参数的顺序要按照 key 值 ASCII 码升序排序
            $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
            $signature = sha1($string);
            $signPackage = array(
              "appId"     => 'wx16eada8cf1729fc4',
              "nonceStr"  => $nonceStr,
              "timestamp" => $timestamp,
              "url"       => $url,
              "signature" => $signature,
              "rawString" => $string
            );
            $this->ajaxReturn($signPackage,'JSON');
        }else{
            $this->ajaxReturn($msg,'JSON');
        }
       
    }

    public function memberCoordinate(){//获取用户经纬度，返回用户再近的门店
        $float=I('post.op');
        $latitude=I('post.latitude');
        $longitude=I('post.longitude');
        if($float=='fruits_api_memberCoordinate'){
            if($latitude && $longitude){//经纬度获取成功查询再近距离的门店
                $store=M('store')->field('Id,latitude,longitude')->order('Id')->select();
                foreach ($store as $key => $value) {
                    $id=$value['Id'];
                    $latitude1=$value['latitude'];
                    $longitude1=$value['longitude'];
                    $len=$this->getDistance($latitude1, $longitude1, $latitude, $longitude);//计算两个坐标的距离
                    $stores[$id]=$len;
                }
                asort($stores);
                $i=0;
                foreach ($stores as $key => $value) {
                        $ids[$i]=$key;
                        $i++;
                }
                $storeid=$ids[0];
                $data=M('store')->field('Id,name')->Where("Id=$storeid")->find();
                $this->ajaxReturn($data,'JSON'); 
            }else{
                $msg=array('error'=>'经纬度获取失败');
                $this->ajaxReturn($msg,'JSON'); 
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON');
        }
    }

    public function coupons(){//首页优惠券
        $float=I('post.op');
        if($float=='fruits_api_coupons'){
            $memberid=1;
            $map=array(
                'memberid'=>$memberid,
                'tpye'=>2,//新用户
            );
            $bool=M('member_coupons')->Where($map)->find();
            if(!$bool){
                $data[0]=M('coupons')->Where('type=2')->find();
            }
            $maps=array(
                'memberid'=>$memberid,
                'type'=>1,
            );
            $couponstime=M('coupons')->Where('type=1')->find();
            $satrt=$couponstime['start_time'];
            $end=$couponstime['end_time'];
            $maps['lq_time']=array('between',"$satrt,$end 23:59:59"); 
            $bools=M('member_coupons')->Where($maps)->find();
            if(!$bools){
                $data[1]=M('coupons')->Where('type=1')->find();
            } 
            if($data){
                $this->ajaxReturn($data,'JSON');
            }else{
                $ts['data']=0;
                $ts['msg']='没有数据';
                $this->ajaxReturn($ts,'JSON');
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON');
        }
    }

    public function jl(){
        echo $this->getDistance(30.526310,114.455520, 30.526310, 114.455523);
    }

    public function BarCode(){
        $barcode = new \Org\Util\BarCode('1305 4413 8579 408522');
        $barcode->createBarCode();
    }
}