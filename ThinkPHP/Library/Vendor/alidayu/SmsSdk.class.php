<?php
// echo dirname(__DIR__);die;
// date_default_timezone_set("Asia/Shanghai");
// ini_set("display_errors", "on");
// use Vendor\alidayu;
// include  $_SERVER['SERVER_NAME'].'/fruits_shop/ThinkPHP/Library/Vendor/alidayu/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
// Config::load();

/**
 * Class SmsDemo
 *
 * 这是短信服务API产品的DEMO程序，直接执行此文件即可体验短信服务产品API功能
 * (只需要将AK替换成开通了云通信-短信服务产品功能的AK即可)
 * 备注:Demo工程编码采用UTF-8
 */
class SmsSdk
{
    static $acsClient = null;
    public function  __construct()
    {
        ini_set("display_errors", "on");
        include_once $_SERVER['SERVER_NAME'].'/fruits_shop/ThinkPHP/Library/Vendor/alidayu/vendor/autoload.php';
        // 加载区域结点配置
        Vendor('alidayu.lib.Core.Config');
        Config::load();
    }
    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = "LTAIOmFlsY9MAeQc"; // AccessKeyId

        $accessKeySecret = "Rmoz89Psu41VR5G18y7faRNreIrRbi"; // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";


        if(static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            Vendor('alidayu.lib.Core.Profile.DefaultProfile');
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            Vendor('alidayu.lib.Core.DefaultAcsClient');
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    /**
     * 发送短信
     * @return stdClass
     */
    public static function sendSms($phone,$code) {

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        Vendor('alidayu.lib.Api.Sms.Request.V20170525.SendSmsRequest');
        $request = new SendSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers("$phone");

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName("打雷网");

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode("SMS_138073775");

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
            "code"=>"$code",
            "product"=>"dsd"
        ), JSON_UNESCAPED_UNICODE));

        // 可选，设置流水号
        $request->setOutId("1234");

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        $request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        $acsResponse =  json_decode( json_encode($acsResponse),true);
        return $acsResponse;
    }

}

// 调用示例：
// set_time_limit(0);
// header('Content-Type: text/plain; charset=utf-8');

// $response = SmsDemo::sendSms();

