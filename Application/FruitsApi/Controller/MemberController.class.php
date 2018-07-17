<?php
namespace FruitsApi\Controller;

/* 新版阿里大于 */
use Vendor\alidayu;
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
            $map['memberid']=$memberid;
            $member=M('member_address')->Where($map)->select();
            //var_dump($member);
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
        $location=I('post.location');
        // $type=I('post.type');
        if($float=='fruits_api_addAddress'){
            if($memberid && $address && $house && $phone && $username && $sex && $location){
                $jwd=explode(',',$location);
                $lon=$jwd[0];
                $lat=$jwd[1];
                $map=array(
                    'memberid'=>$memberid,
                    'address'=>$address,
                    'house'=>$house,
                    'phone'=>$phone,
                    'username'=>$username,
                    'sex'=>$sex,
                    'lon'=>$lon,
                    'lat'=>$lat,
                );
                $bool=M('member_address')->add($map);
                if($bool){
                    $msg=array('flag'=>'1','msg'=>'添加成功');//添加成功
                }else{
                    $msg=array('flag'=>'-1','msg'=>'添加失败');//添加失败
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

    public function alertAddress(){//修改地址默认显示
        $float=I('post.op');
        if($float=='fruits_api_alertAddress'){
            $id=I('post.id');
            $msg=M('member_address')->Where("Id=$id")->find();
        }else{
            $msg=array('msg'=>'身份验证失败');
        }
        $this->ajaxReturn($msg,'JSON');
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
        $location=I('post.location');
        // $type=I('post.type');
        if($float=='fruits_api_editAddress'){
            if($addressid && $address && $house && $phone && $username && $sex && $location){
                $jwd=explode(',',$location);
                $lon=$jwd[0];
                $lat=$jwd[1];
                $map=array(
                    'address'=>$address,
                    'house'=>$house,
                    'phone'=>$phone,
                    'username'=>$username,
                    'sex'=>$sex,
                    'lon'=>$lon,
                    'lat'=>$lat,
                );
                $where=array(
                    'memberid'=>$memberid,
                    'Id'=>$addressid,
                );
                $bool=M('member_address')->Where($where)->save($map);
                if($bool){
                    $msg=array('flag'=>'1','msg'=>'修改成功');//修改成功
                }else{
                    $msg=array('flag'=>'-1','msg'=>'修改失败');//修改失败
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
                    $msg=array('flag'=>'1','msg'=>'添加成功');//添加成功
                }else{
                    $msg=array('flag'=>'-1','msg'=>'添加失败');//添加失败
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
            );
            $bool=M('member_coupons')->Where($map)->find();
            $lq_time=$bool['lq_time'];
            if($lq_time){
                $valid_time=$coupons2['valid_time'];
                $day=$this->getDays($lq_time,date('Y-m-d'));
                if($day>$valid_time){
                    $data['status']=3;//已失效
                    M('member_coupons')->Where($map)->save($data);
                }
            }
            $map['type']=1;
            $satrt=$coupons1['start_time'];
            $end=$coupons1['end_time'];
            $map['lq_time']=array('between',"$satrt,$end 23:59:59");
            $bools=M('member_coupons')->Where($map)->find();
            if($bools){
                $lq_time=$bools['lq_time'];
                $valid_time=$coupons1['valid_time'];
                $day=$this->getDays($lq_time,date('Y-m-d'));
                if($day>$valid_time){
                    $data['status']=3;//已失效
                    M('member_coupons')->Where($map)->save($data);
                }
            }
            $maps=array(
                    'a.memberid'=>$memberid,
                );
            $maps['a.status']=array('in','0,1');
            $coupons=M('member_coupons')
                ->alias('a')
                ->join('coupons b on a.coupons_id=b.Id')
                ->field('b.*,a.status')
                ->Where($maps)
                ->select();
            foreach ($coupons as $key => $value) {
                $lq_time=$value['lq_time'];
                $valid_time=$value['valid_time'];
                $coupons[$key]['end_time']=date('Y-m-d',strtotime("$lq_time +$valid_time day"));
            }
            // var_dump($coupons);
            if($coupons){
                $this->ajaxReturn($coupons,'JSON');
            }else{
                $this->ajaxReturn(0,'JSON');
            }
        }else{
            $msg=array('error'=>'身份验证失败');
            $this->ajaxReturn($msg,'JSON');
        }
    }

    public function lqCoupons(){//用户领取优惠券
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_lqCoupons'){
            $id=I('post.id');
            $map=array(
                'status'=>1,
                'lq_time'=>date("Y-m-d"),
            );
            $bool=M('member_coupons')->Where("Id=$id")->save($map);
            if($bool){
                $msg=array('flag'=>1,'msg'=>'成功');
            }else{
                $msg=array('flag'=>-1,'msg'=>'失败');
            }
        }else{
            $msg=array('error'=>'身份验证失败');
        }
        $this->ajaxReturn($msg,'JSON');
    }
    //获取用户卡号
    public function getMemberID(){
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_getMemberID'){
            $card=M('member')->field('sCardID,type')->Where("Id=$memberid")->find();
            if($card['type']==1){//是会员
                $cards=$this->getCardID($card['sCardID']);
                $msg=array('cardID'=>$cards,'flag'=>1,'msg'=>'会员');
            }else{
                $msg=array('flag'=>-1,'msg'=>'非会员');
            }
        }else{
            $msg=array('error'=>'身份验证失败'); 
        }
        $this->ajaxReturn($msg,'JSON');
    }

    //获取验证码
    public function getYzm(){
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_getYzm'){
            $phone=I('post.phone');
            if($phone){
                $bool=M('member')->Where("sMobile='$phone'")->find();//查看手机号是否存在
                if($bool){
                    $msg=array(
                        'flag'=>'2',
                        'msg'=>'该手机号已注册',
                    );
                }else{
                    $shuzi=range(0,9);//生成数组
                    shuffle($shuzi);  //按随机顺序重新排序
                    $s=array_slice($shuzi, 0,6);//取出数组6位
                    $yzm =join($s,'');//把数组组合成字符串
                    import('Vendor.alidayu.SmsSdk');//导入阿里短信接口
                    $alidayu=new \SmsSdk();
                    $acsResponse=$alidayu->sendSms($phone,$yzm);
                    if($acsResponse['Code']=='OK'){
                        $msg=array(
                            'flag'=>1,
                            'msg'=>'发送成功',
                            'yzm'=>$yzm,
                        );
                    }else{
                        $msg=array(
                            'flag'=>'-1',
                            'msg'=>'发送失败',
                            'alits'=>$acsResponse,
                        );
                    }
                }
            }else{
                $msg=array('error'=>'缺少手机号参数');  
            }
        }else{
            $msg=array('error'=>'身份验证失败');  
        }
        $this->ajaxReturn($msg,'JSON');
    }

    //用户注册
    public function memberRegister(){
        $float=I('post.op');
        $memberid=1;
        if($float=='fruits_api_memberRegister'){
            $phone=I('post.phone');
            $sVIPName=I('post.sVIPName');
            $birthday=I('post.birthday');
            if($phone && $sVIPName && $birthday){
                $result=$this->get_logion();//接口登陆返回值
                if($result['Status']==1){//登陆成功
                    $url='http://124.225.146.25:3003/crm.asmx/Get_VIP';
                    $data=array(
                        'sMobile'=>$phone,
                        'sCardID'=>'',
                        'sWXOpenID'=>'',
                    );
                    $member=$this->get_port($url,$data);//获取用户线下信息
                    if($member){//线下有用户信息
                        $birthday=$member['生日'];
                        $iBirthMon=date('m',strtotime($birthday));
                        $iBirthDayA=date('d',strtotime($birthday));
                        $sexs=$member['性别'];
                        if($sexs=='先生'){
                            $sex=1;
                        }else{
                            $sex=2;
                        }
                        $map=array(
                            'sVIPName'=>$member['会员名'],
                            'sCardID'=>$member['会员卡号'],
                            'sAddress'=>$member['地址'],
                            'sSex'=>$sex,
                            'dBirthday'=>$member['生日'],
                            'sMobile'=>$member['手机号'],
                            'createtime'=>$member['办理时间'],
                            'sShpCode'=>$member['办理店号'],
                            'sTypeID'=>$member['卡类别'],
                            're_integral'=>$member['卡内积分'],
                            'balance'=>$member['卡内余额'],
                            'start_time'=>$member['开始时间'],
                            'end_time'=>$member['结束时间'],
                            'iBirthMon'=>$iBirthMon,
                            'iBirthDayA'=>$iBirthDayA,
                            'type'=>1,
                        );
                        $bool=M('member')->Where("Id=$memberid")->save($map);
                        if($bool){
                            $msg=array('flag'=>'1','msg'=>'注册成功');
                            session_start();
                            $_SESSION['member_type']=1;//储层当前用户是否为状态
                        }else{
                            $msg=array('flag'=>'-1','msg'=>'注册失败');
                        }
                    }else{//线下无用户信息
                        $storeid=$_SESSION['storeid'];
                        $store=M('store')->field('store_num')->Where('Id=$storeid')->find();
                        $iBirthMon=date('m',strtotime($birthday));
                        $iBirthDayA=date('d',strtotime($birthday));
                        $url='http://124.225.146.25:3003/crm.asmx/Get_CardIDByAdd';
                        $cardID=$this->get_port($url);//获取用户有效卡号
                        $map=array(
                            'sVIPName'=>$sVIPName,
                            'dBirthday'=>$birthday,
                            'sMobile'=>$phone,
                            'createtime'=>date("Y-m-d H:i:s"),
                            'sShpCode'=>$store['store_num'],
                            'iBirthMon'=>$iBirthMon,
                            'iBirthDayA'=>$iBirthDayA,
                            'sCardID'=>$cardID,
                            'type'=>1,
                        );
                        $bool=M('member')->Where("Id=$memberid")->save($map);
                        $member=M('member')->Where("Id=$memberid")->find();
                        if($member['sSex']=='1'){
                            $sex='先生';
                        }else{
                            $sex='女士';
                        }
                        $data=array(
                            'sVIPName'=>$sVIPName,
                            'sCardID'=>$member['sCardID'],
                            'sTypeID'=>0,
                            'sSex'=>$sex,
                            'sMobile'=>$phone,
                            'sWXOpenID'=>$member['sWXOpenID'],
                            'sWXName'=>$member['sWXName'],
                            'iBirthMon'=>$iBirthMon,
                            'iBirthDayA'=>$iBirthDayA,
                            'sPhone'=>'',
                            'sIDCard'=>'',
                            'sAddress'=>'',
                            'dBirthday'=>'',
                            'sEmail'=>'',
                            'sMemo'=>'',
                            'sShpCode'=>'',
                            'sReCardID'=>'',
                            'sHealImgURL'=>$member['sHealImgURL'],
                            'sChannel'=>'',
                        );
                        $url='http://124.225.146.25:3003/crm.asmx/Add_VIP';
                        $add=$this->get_port($url,$data);//同步线下
                        if($bool && $add[0]['Status']=='1'){
                            $msg=array('flag'=>'1','msg'=>'注册成功');
                            session_start();
                            $_SESSION['member_type']=1;//储层当前用户是否为会员状态
                        }else{
                            $msg=array('flag'=>'-1','msg'=>'注册失败');
                        }
                    }
                }else{
                   $msg=array('error'=>'登陆线下接口失败'); 
                }
            }else{
                $msg=array('error'=>'缺少参数'); 
            }
        }else{
            $msg=array('error'=>'身份验证失败'); 
        }
        $this->ajaxReturn($msg,'JSON');
    }

    public function createOrder(){
        $url='https://peisongopen.meituan.com/api/order/createByShop '; //生成美团订单接口地址
        $data=array(
            'appkey'=>'fc236e768eb142b39c7359f4f833bfa7',
            'timestamp'=>time(),
            'version'=>'1.0',
            'delivery_id'=>'123454558',
            'order_id'=>'14',
            'shop_id'=>'test_0001',
            'delivery_service_code'=>'4011',
            'receiver_name'=>'猜猜猜',
            'receiver_address'=>'武汉',
            'receiver_phone'=>'13516896325',
            'receiver_lng'=>'116398419',
            'receiver_lat'=>'39985005',
            'coordinate_type'=>'0',
            'goods_value'=>'20.00',
            'goods_weight'=>'10.23',
        );
        $result=$this->get_mt_prot($url,$data);
        var_dump($result);die;
    }

    public  function queryOrderStatus()
    {
        $url='https://peisongopen.meituan.com/api/order/status/query'; //生成美团订单接口地址
        $data=array(
            'appkey'=>'fc236e768eb142b39c7359f4f833bfa7',
            'timestamp'=>time(),
            'version'=>'1.0',
            'delivery_id'=>'123454558',
            'mt_peisong_id'=>'1531123618092955',
        );
        $result=$this->get_mt_prot($url,$data);
        var_dump($result);die;
    }

    public function deleteOrder(){
        $url='https://peisongopen.meituan.com/api/test/order/rearrange'; //生成美团订单接口地址
        $data=array(
            'appkey'=>'fc236e768eb142b39c7359f4f833bfa7',
            'timestamp'=>time(),
            'version'=>'1.0',
            'delivery_id'=>'123454558',
            'mt_peisong_id'=>'1531123618092955',
        );
        $result=$this->get_mt_prot($url,$data);
        var_dump($result);die;
    }
    
}