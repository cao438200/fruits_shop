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
class MemberController extends Controller {

    public function myself(){//我的
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_myself'){
            $map['Id']=$memberid;
            $member=M('member')->field('sHealImgURL,sWXName,sVIPName,balance,re_integral')->Where($map)->find();
            $this->ajaxReturn($member,'JSON');
        } else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }
    
    public function myAddress(){//我的地址
        $float=I('post.op');
        $memberid=1;
        $msg=array('error'=>'身份验证失败');
        if($float=='fruits_api_myAddress'){
            $map['Id']=$memberid;
            $member=M('member_address')->Where($map)->select();
            foreach ($member as $key => $value) {
                if($value['type']==1){
                    $member[$key]['type_name']='家';
                }elseif($member['type']==2){
                    $member[$key]['type_name']='公司';
                }elseif($member['type']==3){
                    $member[$key]['type_name']='学校';
                }
            }
            $this->ajaxReturn($member,'JSON');
        } else{
            $this->ajaxReturn($msg,'JSON');
        }  
    }

    public function addAddress(){//添加地址
        $float=I('post.op');
        $memberid=1;
        $address=I('post.address');
        $house=I('post.house');
        $phone=I('post.phone');
        $username=I('post.username');
        $sex=I('post.sex');
        $type=I('post.type');
        if($float=='fruits_api_myAddress'){
            if($memberid && $address && $house && $phone && $username && $sex && $type){
                $map=array(
                    'memberid'=>$memberid,
                    'address'=>$address,
                    'house'=>$house,
                    'phone'=>$phone,
                    'username'=>$username,
                    'sex'=>$sex,
                    'type'=>$type,
                );
                $bool=M('member_address')->add($map);
                if($bool){
                    $msg=array('data'=>'1','msg'=>'添加成功');//添加成功
                }else{
                    $msg=array('data'=>'-1','msg'=>'添加失败');//添加失败
                }
                $this->ajaxReturn($msg,'JSON');   
            }else{
                $msg=array('error'=>'缺少参数');
                $this->ajaxReturn($msg,'JSON'); 
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON'); 
        } 
    }

    public function editAddress(){//修改地址
        $float=I('post.op');
        $memberid=1;
        $addressid=I('post.addressid');//地址id
        $address=I('post.address');
        $house=I('post.house');
        $phone=I('post.phone');
        $username=I('post.username');
        $sex=I('post.sex');
        $type=I('post.type');
        if($float=='fruits_api_myAddress'){
            if($addressid && $memberid && $address && $house && $phone && $username && $sex && $type){
                $map=array(
                    'address'=>$address,
                    'house'=>$house,
                    'phone'=>$phone,
                    'username'=>$username,
                    'sex'=>$sex,
                    'type'=>$type,
                );
                $where=array(
                    'memberid'=>$memberid,
                    'id'=>$addressid,
                );
                $bool=M('member_address')->Where($where)->save($map);
                if($bool){
                    $msg=array('data'=>'1','msg'=>'修改成功');//修改成功
                }else{
                    $msg=array('data'=>'-1','msg'=>'修改失败');//修改失败
                }
                $this->ajaxReturn($msg,'JSON'); 
            }else{
                $msg=array('error'=>'缺少参数');
                $this->ajaxReturn($msg,'JSON'); 
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON'); 
        } 
    }

    public function memberOpinion(){//添加意见反馈
        $float=I('post.op');
        $memberid=1;
        $desc=I('post.desc');
        if($float=='fruits_api_memberOpinion'){
            if($desc){
                $map=array(
                    'desc'=>$desc,
                    'createtime'=>date('Y-m-d H:i:s'),
                    'memberid'=>$memberid,
                );
                $bool=M('member_opinion')->add($map);
                if($bool){
                    $msg=array('data'=>'1','msg'=>'添加成功');//添加成功
                }else{
                    $msg=array('data'=>'-1','msg'=>'添加失败');//添加失败
                }
                $this->ajaxReturn($msg,'JSON'); 
            }else{
               $msg=array('error'=>'缺少参数');
                $this->ajaxReturn($msg,'JSON');  
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON');
        }

    }

    public function memberCoupons(){//用户优惠券
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_memberCoupons'){
            $coupons1=M('coupons')->Where('type=1')->find();//特殊时间优惠券
            $coupons2=M('coupons')->Where('type=2')->find();//新用户优惠券
            $map=array(
                'memberid'=>$memberid,
                'tpye'=>2,//新用户
                'status'=>1,
            );
            $bool=M('member_coupons')->Where($map)->find();
            if($bool){
                $lq_time=$bool['lq_time'];
                $valid_time=$coupons2['valid_time'];
                $day=$this->getDays($lq_time,date('Y-m-d H:i:s'));
                if($day>$valid_time){
                    $data['status']=3;//已失效
                    M('member_coupons')->Where($map)->save($data);
                }
                $maps=array(
                    'a.memberid'=>$memberid,
                    'a.type'=>2,
                );
                $coupons[0]=M('member_coupons')
                        ->alias('a')
                        ->join('coupons b on a.coupons_id=b.Id')
                        ->field('b.*,a.status')
                        ->Where($maps)
                        ->find();
            }

            $map['type']=1;
            $satrt=$coupons1['start_time'];
            $end=$coupons1['end_time'];
            $map['lq_time']=array('between',"$satrt,$end 23:59:59");
            $bools=M('member_coupons')->Where($map)->find();
            if($bools){
                $lq_time=$bools['lq_time'];
                $valid_time=$coupons1['valid_time'];
                $day=$this->getDays($lq_time,date('Y-m-d H:i:s'));
                if($day>$valid_time){
                    $data['status']=3;//已失效
                    M('member_coupons')->Where($map)->save($data);
                }
                $maps=array(
                    'a.memberid'=>$memberid,
                    'a.type'=>1,
                );
                $maps['a.lq_time']=array('between',"$satrt,$end 23:59:59");
                $coupons[1]=M('member_coupons')
                        ->alias('a')
                        ->join('coupons b on a.coupons_id=b.Id')
                        ->field('b.*,a.status')
                        ->Where($maps)
                        ->find();
            }
            // var_dump($coupons);
            if($coupons){
                $this->ajaxReturn($coupons,'JSON');
            }else{
                $msg=array('data'=>'0','msg'=>'没有数据');
                $this->ajaxReturn($msg,'JSON');
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON');
        }
    }

    public function getCardID(){
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_getCardID'){
            $card=M('member')->field('sCardID')->Where("Id=$memberid")->find();
            if($card){
                $this->ajaxReturn($card,'JSON');
            }else{
                $msg=array('data'=>'0','msg'=>'没有数据');
                $this->ajaxReturn($msg,'JSON');
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON');
        }
    }

}