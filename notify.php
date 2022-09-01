<?php
//error_reporting(0);
header('Content-Type:text/plain; charset=utf-8');
require 'vendor/autoload.php';
require_once 'config.php';
require_once 'init.php';

use Alipay\EasySDK\Kernel\Factory;
use EasyWeChat\Kernel\Message;


if(isset($_POST['out_trade_no']) && Factory::payment()->common()->verifyNotify($_POST)) {
    $out_trade_no = $_POST['out_trade_no'];

	//支付宝交易号

	$trade_no = $_POST['trade_no'];

	//交易状态
	$trade_status = $_POST['trade_status'];

	$passback_params = json_decode(base64_decode(urldecode($_POST['passback_params'])), true);

	$notify_url = $passback_params['notify_url'];
    $postdata = http_build_query($_POST);

	$options = array(

		'http' => array(

		'method' => 'POST',

		'header' => 'Content-type:application/x-www-form-urlencoded',

		'content' => http_build_query(array(
			"out_trade_no" => $_POST['out_trade_no'],
			"trade_no" => $_POST['trade_no'],
			"total_amount" => $_POST['total_amount'],
			"receipt_amount" => $_POST['receipt_amount'],
			"trade_status" => $_POST['trade_status'],
			"sign" => md5($_POST['out_trade_no'].$_POST['trade_no'].$_POST['total_amount'].$_POST['receipt_amount'].$_POST['trade_status'].$config['md5_secret']),
		)),

		'timeout' => 15 * 60 // 超时时间（单位:s）

		)

	);

	$context = stream_context_create($options);

	$result = file_get_contents($notify_url, false, $context);

    echo "success";
} else {
	$server = $wechat->getServer();
	$server->handlePaid(function (Message $message, \Closure $next) use($wechat, $config) {
		$response = $message->toArray();
		if ($response['trade_state'] == 'SUCCESS') {
			$passback_params = json_decode($response['attach'], true);

			$notify_url = $passback_params['notify_url'];
			$postdata = http_build_query($response);
			
			$response['out_trade_no'] = str_replace('wechat_', '', $response['out_trade_no']);

			$options = array(
				'http' => array(
				'method' => 'POST',
				'header' => 'Content-type:application/x-www-form-urlencoded',

				'content' => http_build_query(array(
					'response' => base64_encode(json_encode($response)),
					"sign" => md5(base64_encode(json_encode($response)).$config['md5_secret']),
				)),

				'timeout' => 15 * 60 // 超时时间（单位:s）

				)

			);

			$context = stream_context_create($options);

			$result = file_get_contents($notify_url, false, $context);

		}
		

		return $next($message);
	});
	
	// 默认返回 ['code' => 'SUCCESS', 'message' => '成功']
	return $server->serve();
}
