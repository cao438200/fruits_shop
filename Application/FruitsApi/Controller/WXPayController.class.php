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
class WXPayController extends Controller {

	
    /**
     * notify_url接收页面
     */
    public function notify(){
        // 导入微信支付sdk
        Vendor('Weixinpay.Weixinpay');
        $wxpay=new \Weixinpay();
        $result=$wxpay->notify();
        if ($result) {
            // 验证成功 修改数据库的订单状态等 $result['out_trade_no']为订单号
            
        }
    }

    /**
     * 公众号支付 必须以get形式传递 out_trade_no 参数
     * 示例请看 /Application/Home/Controller/IndexController.class.php
     * 中的weixinpay_js方法
     */
    public function pay(){
        // 导入微信支付sdk
        Vendor('Weixinpay.Weixinpay');
        $wxpay=new \Weixinpay();
        // 获取jssdk需要用到的数据
        $data=$wxpay->getParameters();
        // 将数据分配到前台页面
        $assign=array(
            'data'=>json_encode($data)
            );
        $this->assign($assign);
        $this->display();
    }

}