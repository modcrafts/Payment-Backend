<?php
error_reporting(0);
header('Content-Type:application/json; charset=utf-8');
require 'vendor/autoload.php';
require_once 'config.php';
require_once 'init.php';
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;

try {

    $sign = md5($_POST['out_trade_no'].$_POST['subject'].$_POST['type'].$_POST['total_amount'].$_POST['notify_url'].$_POST['return_url'].$config['md5_secret']);
    if($sign != $_POST['sign'])
	{
		echo(json_encode(array("status"=>"failed","message"=>"Invaild Sign!")));
        die;
	}

	//商户订单号，商户网站订单系统中唯一订单号
    $out_trade_no = trim($_POST['out_trade_no']);

    //订单名称
    $subject = trim($_POST['subject']);

    //付款金额
    $total_amount = floatval(trim($_POST['total_amount']));
    $passback_params = urlencode(base64_encode(json_encode(array(
        'notify_url' => trim($_POST['notify_url'])
        ))));

    //发起API调用
    if ($_POST['type'] == "wechat") {
        $result = $wechat->getClient()->postJson('v3/pay/transactions/native', [
            'mchid' => (string)$wechat->getMerchant()->getMerchantId(),
            'appid' => $config['wechat']['app_id'],
            'description' => $subject,
            'out_trade_no' => "wechat_".$out_trade_no,
            'attach' => json_encode(array(
                'notify_url' => trim($_POST['notify_url'])
                )),
            'notify_url' => $config['wechat']['notify_url'],
            'amount' => [
                'total' => (int)((float)$total_amount*100),
            ],
        ]);
    } elseif ($_POST['type'] == "alipay_wap") {
        $result = Factory::payment()->wap()
                ->optional('passback_params',$passback_params)
                ->pay($subject, $out_trade_no, $total_amount, str_replace(parse_url($_POST['return_url'])['path'], "", $_POST['return_url']) , $_POST['return_url']);
    } elseif ($_POST['type'] == "alipay_pc") {
        $result = Factory::payment()->page()
                ->optional('passback_params',$passback_params)
                ->pay($subject, $out_trade_no, $total_amount, $_POST['return_url']);
    } else {
        die;
    }

    //处理响应或异常
    if ($_POST['type'] == "wechat") {
        $response = $result->toArray(false);
        //print_r($response);
        echo json_encode(array(
            "status" => "success",
            "message" => json_encode($response),
            'code_url' => base64_encode((string)$response['code_url']),
            'sign' => md5(base64_encode((string)$response['code_url']).$config['md5_secret']),
        ));
        
    } else {
        $responseChecker = new ResponseChecker();
    
        if ($responseChecker->success($result)) {
            echo json_encode(array(
                "status" => "success",
                "message" => "",
                'content' => base64_encode($result->body),
                'sign' => md5(base64_encode($result->body).$config['md5_secret']),
            ));
        } else {
            echo json_encode(array(
                "status" => "failed",
                "message" => $result->msg."，".$result->subMsg
            ));
        }
    }
    
    
} catch (Exception $e) {
    echo json_encode(array(
        "status" => "failed",
        "message" => $e->getMessage()
    ));
}