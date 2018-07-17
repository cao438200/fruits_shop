<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
date_default_timezone_set('Asia/Shanghai');
/**
 * ThinkPHP 控制器基类 抽象类
 */
abstract class Controller {

    /**
     * 视图实例对象
     * @var view
     * @access protected
     */    
    protected $view     =  null;

    /**
     * 控制器参数
     * @var config
     * @access protected
     */      
    protected $config   =   array();

   /**
     * 架构函数 取得模板对象实例
     * @access public
     */
    public function __construct() {
        Hook::listen('action_begin',$this->config);
        //实例化视图类
        $this->view     = Think::instance('Think\View');
        //控制器初始化
        if(method_exists($this,'_initialize'))
            $this->_initialize();
    }


    //获取两个经纬度之间的距离
    function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000;    
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }

    //获取订单配送费
    function getPsFree($len,$weight){
        $free=5.3;
        $d=date("Y-m-d");
        $d0=strtotime(date('Y-m-d H:i'));
        $d1=strtotime("$d 11:00");//午高峰
        $d2=strtotime("$d 12:59");
        $d3=strtotime("$d 21:00");//晚高峰
        $d4=strtotime("$d 23:59");
        $d5=strtotime("$d 00:00");
        $d6=strtotime("$d 05:59");
        if($d1<$d0 && $d0<$d2){
            $free=$free+2;//午高峰
        };
        if($d3<$d0 && $d0<$d4){
            $free=$free+2;//晚高峰
        };
        if($d5<$d0 && $d0<$d6){
            $free=$free+2;//晚高峰
        };

        //距离收费
        if(1<$len && $len<=3){
            $free=1*($len-1)+$free;
        }else if(3<$len && $len<=5){
            $free=2+2*($len-3)+$free;
        }else if(5<$len && $len<=7){
            $free=6+3*($len-5)+$free;
        }else if(7<$len && $len<=10){
            $free=12+5*($len-7)+$free;
        }

        //重量收费
        if(5<$weight && $weight<=10){
            $free=0.5*($weight-5)+$free;
        }else if(10<$weight && $weight<=20){
            $free=2.5+1*($weight-10)+$free;
        }else if(10<$weight && $weight<=20){
            $free=12.5+2*($weight-20)+$free;
        }

        return $free;
    }
    
    ////自定义函数,get访问url返回结果
     function https_request($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl,  CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        $data = json_decode($data, true);
        if (curl_errno($curl)){
            return 'ERROR'.curl_error($curl);
        }
        curl_close($curl);
        return $data;
    }

    //获取微信的access_token;
    public function get_access_token()  
    {  
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx25b01b557045ea0a&secret=bb8e78a44d4e6dc236301cc6cf8b7635';
        $token=M('access_token')->find();
        if(!$token){
            $jsoninfo =$this->https_request($url);
            $map['access_token']=$jsoninfo['access_token'];
            $map['expires_time']=$jsoninfo['expires_in']+time()-200;
            M('access_token')->add($map);
            return $jsoninfo['access_token'];
        }else if($token['expires_in']<time()){
            $jsoninfo =$this->https_request($url);
            $map['access_token']=$jsoninfo['access_token'];
            $map['expires_time']=$jsoninfo['expires_in']+time()-200;
            $where['expires_time']=$token['expires_time'];
            M('access_token')->Where($where)->save($map);//时间过期更新
            return $jsoninfo['access_token'];
        }else{
            return $token['access_token'];
        }   
    }

    //获取微信的jsapi_ticket
    public function get_jsapi_ticket()  
    {  

        $accessToken=$this->get_access_token();
        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $ticket=M('jsapi_ticket')->find();
        if(!$ticket){
            $jsoninfo =$this->https_request($url);
            $map['ticket']=$jsoninfo['ticket'];
            $map['expires_time']=$jsoninfo['expires_in']+time()-200;
            M('jsapi_ticket')->add($map);
            return $jsoninfo['ticket'];
        }else if($ticket['expires_in']<time()){
            $jsoninfo =$this->https_request($url);
            $map['ticket']=$jsoninfo['ticket'];
            $map['expires_time']=$jsoninfo['expires_in']+time()-200;
            $where['expires_time']=$ticket['expires_time'];
            M('jsapi_ticket')->Where($where)->save($map);//时间过期更新
            return $jsoninfo['ticket'];
        }else{
            return $ticket['ticket'];
        }   
    }

    //随机字符串
    function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    //字符串字典顺序排序
    public function bigger($s1, $s2) {
        $length1 = strlen($s1);
        $length2 = strlen($s2);
        $i = 0;
        while ($i < $length1 && $i < $length2) {
            if ($s1[$i] > $s2[$i]) {
                return true;
            } else if ($s1[$i] < $s2[$i]) {
                return false;
            } else {
                $i++;
            }
        }
        if ($i == $length1) {
            return false;
        } else {
            return true;
        }
    }
    //美团签名算法
    public  function sign_mt($data)
    { 
        $length=count($data); $k=0;
        foreach ($data as $key => $value) {//取出数组键值组成数组
            $keys[$k]=$key;
            $k++;
        }
        for ( $i = 0; $i < $length-1; $i++) {//冒泡排序
            for ($j = 0; $j < $length-1-$i; $j++) {
                $flag=$this->bigger($keys[$j], $keys[$j + 1]);
                if ($flag) {
                    $tmp = $keys[$j];
                    $keys[$j] = $keys[$j + 1];
                    $keys[$j + 1] = $tmp;
                }
            }
        }
        foreach ($keys as $key => $value) {//根据排序后的键值得到排序后的数组
            $datas[$value]=$data[$value];
        }
        foreach ($datas as $key => $value) {//以参数参数值拼接得到加密前参数
            if(strlen($value)>0){
                $strs .="$key"."$value";
            }
        }
        $str=sha1("j}S,5,2q.]X:9oCg5J.?p*[X:8>e6c}z56tN)/ePiE5}96HPd(k%9<i8P[1x@tOG"."$strs");//secret + 排序后的参数sha1加密
        return $str;
    }

    //访问美团接口
    
    public function get_mt_prot($url,$data){
        $sign=$this->sign_mt($data);//获取美团签名
        $data['sign']=$sign;
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl);
        $result=json_decode($result,true);
        return $result;
    }

    //获取两个时间的天数差
    function getDays ($day1, $day2){
      $second1 = strtotime($day1);
      $second2 = strtotime($day2); 
      if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
      }
      return ($second1 - $second2) / 86400;
    }

    //调用线下接口方法
    public  function get_port($url,$data=''){
        $data=http_build_query($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if($data){ 
            curl_setopt($curl, CURLOPT_POST, 1);  
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        $result=json_decode($result,true);
        return $result;
    }
    //登陆接口
    public function get_logion_status(){ 
        $url="http://211.149.155.236:90/order.asmx/ABC_Login";
        $data=array(
            'sUserID'=>'admin',
            'sPassword'=>'123456',
            'sExportType'=>'JSON',
            'sCharsetName'=>'UTF-8',
        );
        return $this->get_port($url,$data);
    }

    public function get_logion(){ 
        $url="http://124.225.146.25:3003/crm.asmx/ABC_Login";
        $data=array(
            'sUserID'=>'admin',
            'sPassword'=>'rq2d3333',
            'sExportType'=>'JSON',
            'sCharsetName'=>'UTF-8',
        );
        return $this->get_port($url,$data);
    }
    //获取卡号
    public function getCardID($card){
        $y=date('y');//当前年
        $m=date('m');//当前月
        $h=date('H');//当前时
        $i=date('i');//当前分
        if($i+5>59){
            $i=$i+5-60;
            $i='0'.$i;
            $h=$h+1;
            if($h<10){
                $h='0'.$h;
            }
        }
        if(strlen($card)%2==1){
            $card='0'.$card;
        }
        $cards=$card.$y.$m.$h.$i;//第一步
        // var_dump($cards);die;
        $cards=str_split($cards);//字符串转化数组
        $legth=count($cards);
        for($i=0; $i <$legth ; $i++) { //第二步数字替换
            if($cards[$i]==0){
                $cards[$i]=2;
            }elseif ($cards[$i]==1) {
                $cards[$i]=3;
            }elseif ($cards[$i]==2) {
                $cards[$i]=5;
            }elseif ($cards[$i]==3) {
                $cards[$i]=9;
            }elseif ($cards[$i]==4) {
                $cards[$i]=0;
            }elseif ($cards[$i]==5) {
                $cards[$i]=8;
            }elseif ($cards[$i]==6) {
                $cards[$i]=4;
            }elseif ($cards[$i]==7) {
                $cards[$i]=7;
            }elseif ($cards[$i]==8) {
                $cards[$i]=6;
            }elseif ($cards[$i]==9) {
                $cards[$i]=1;
            }
        }
        $cards=array_reverse($cards);//第三步反转
        $legths=floor($legth/2);
        for($i=0; $i <$legths ; $i++){//卡号从左至右前后对调位置
            $center=$cards[2*$i];
            $cards[2*$i]=$cards[2*$i+1];
            $cards[2*$i+1]=$center;
        }
        foreach($cards as $key => $value) {//数组转字符串
            $cardID .=$value;
        }
        return $cardID;
    }
    /**
     * 模板显示 调用内置的模板引擎显示方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $content 输出内容
     * @param string $prefix 模板缓存前缀
     * @return void
     */
    protected function display($templateFile='',$charset='',$contentType='',$content='',$prefix='') {
        $this->view->display($templateFile,$charset,$contentType,$content,$prefix);
    }

    /**
     * 输出内容文本可以包括Html 并支持内容解析
     * @access protected
     * @param string $content 输出内容
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $prefix 模板缓存前缀
     * @return mixed
     */
    protected function show($content,$charset='',$contentType='',$prefix='') {
        $this->view->display('',$charset,$contentType,$content,$prefix);
    }

    /**
     *  获取输出页面内容
     * 调用内置的模板引擎fetch方法，
     * @access protected
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @param string $content 模板输出内容
     * @param string $prefix 模板缓存前缀* 
     * @return string
     */
    protected function fetch($templateFile='',$content='',$prefix='') {
        return $this->view->fetch($templateFile,$content,$prefix);
    }

    /**
     *  创建静态页面
     * @access protected
     * @htmlfile 生成的静态文件名称
     * @htmlpath 生成的静态文件路径
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @return string
     */
    protected function buildHtml($htmlfile='',$htmlpath='',$templateFile='') {
        $content    =   $this->fetch($templateFile);
        $htmlpath   =   !empty($htmlpath)?$htmlpath:HTML_PATH;
        $htmlfile   =   $htmlpath.$htmlfile.C('HTML_FILE_SUFFIX');
        Storage::put($htmlfile,$content,'html');
        return $content;
    }

    /**
     * 模板主题设置
     * @access protected
     * @param string $theme 模版主题
     * @return Action
     */
    protected function theme($theme){
        $this->view->theme($theme);
        return $this;
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param mixed $name 要显示的模板变量
     * @param mixed $value 变量的值
     * @return Action
     */
    protected function assign($name,$value='') {
        $this->view->assign($name,$value);
        return $this;
    }

    public function __set($name,$value) {
        $this->assign($name,$value);
    }

    /**
     * 取得模板显示变量的值
     * @access protected
     * @param string $name 模板显示变量
     * @return mixed
     */
    public function get($name='') {
        return $this->view->get($name);      
    }

    public function __get($name) {
        return $this->get($name);
    }

    /**
     * 检测模板变量的值
     * @access public
     * @param string $name 名称
     * @return boolean
     */
    public function __isset($name) {
        return $this->get($name);
    }

    /**
     * 魔术方法 有不存在的操作的时候执行
     * @access public
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed
     */
    public function __call($method,$args) {
        if( 0 === strcasecmp($method,ACTION_NAME.C('ACTION_SUFFIX'))) {
            if(method_exists($this,'_empty')) {
                // 如果定义了_empty操作 则调用
                $this->_empty($method,$args);
            }elseif(file_exists_case($this->view->parseTemplate())){
                // 检查是否存在默认模版 如果有直接输出模版
                $this->display();
            }else{
                E(L('_ERROR_ACTION_').':'.ACTION_NAME);
            }
        }else{
            E(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function error($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     */
    protected function success($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @param int $json_option 传递给json_encode的option参数
     * @return void
     */
    protected function ajaxReturn($data,$type='',$json_option=0) {
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                // header("Access-Control-Allow-Origin: *");
                exit(json_encode($data,$json_option));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data));
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                exit($handler.'('.json_encode($data,$json_option).');');  
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);            
            default     :
                // 用于扩展其他返回格式数据
                Hook::listen('ajax_return',$data);
        }
    }

    /**
     * Action跳转(URL重定向） 支持指定模块和延时跳转
     * @access protected
     * @param string $url 跳转的URL表达式
     * @param array $params 其它URL参数
     * @param integer $delay 延时跳转的时间 单位为秒
     * @param string $msg 跳转提示信息
     * @return void
     */
    protected function redirect($url,$params=array(),$delay=0,$msg='') {
        $url    =   U($url,$params);
        redirect($url,$delay,$msg);
    }

    /**
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param Boolean $status 状态
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @access private
     * @return void
     */
    private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
        if(true === $ajax || IS_AJAX) {// AJAX提交
            $data           =   is_array($ajax)?$ajax:array();
            $data['info']   =   $message;
            $data['status'] =   $status;
            $data['url']    =   $jumpUrl;
            $this->ajaxReturn($data);
        }
        if(is_int($ajax)) $this->assign('waitSecond',$ajax);
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);
        // 提示标题
        $this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);   // 状态
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            $this->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','1');
            // 默认操作成功自动返回操作前页面
            if(!isset($this->jumpUrl)) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(C('TMPL_ACTION_SUCCESS'));
        }else{
            $this->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!isset($this->jumpUrl)) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(C('TMPL_ACTION_ERROR'));
            // 中止执行  避免出错后继续执行
            exit ;
        }
    }

   /**
     * 析构方法
     * @access public
     */
    public function __destruct() {
        // 执行后续操作
        Hook::listen('action_end');
    }
}
// 设置控制器别名 便于升级
class_alias('Think\Controller','Think\Action');
