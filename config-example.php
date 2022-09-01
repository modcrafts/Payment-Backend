<?php

$config = [
	'md5_secret' => "通信密钥",

	//支付宝
	'alipay' => [	
		//应用ID,您的APPID。
		'app_id' => "支付宝生产环境 AppId",
        'app_id_dev' => "支付宝测试环境 AppId",

		//商户私钥
		'merchant_private_key' => "支付宝商户私钥",
		
		//异步通知地址
		'notify_url' => "http://域名/notify.php",
		
		//同步跳转
		//'return_url' => "",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayHost' => "openapi.alipay.com",
        'gatewayHostDev' => "openapi.alipaydev.com",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "正式公钥",
		'alipay_public_key_dev' => "测试公钥",

        'encrypt_key' => "<-- 请填写您的AES密钥，例如：aa4BtZ4tspm2wnXLb1ThQA== -->",//非必填
        
	],

	//微信
	'wechat' => [
		'app_id' => '微信AppId',

		//商户ID
		'mch_id' => 0, 
	
		// 商户证书
		/*
		!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		!			请务必保护好密钥防止被他人访问			!
		!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		*/
		'private_key' => __DIR__ . '/certs/apiclient_key.pem',
		'certificate' => __DIR__ . '/certs/apiclient_cert.pem',
	
		 // v3 API 秘钥
		'secret_key' => '',
	
		// v2 API 秘钥
		'v2_secret_key' => '',
	
		// 平台证书：微信支付 APIv3 平台证书，需要使用工具下载
		// 下载工具：https://github.com/wechatpay-apiv3/CertificateDownloader
		'platform_certs' => [
			__DIR__ . '/certs/wechatpay_xxxx.pem',
		],
	
		/**
		 * 接口请求相关配置，超时时间等，具体可用参数请参考：
		 * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
		 */
		'http' => [
			'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
			'timeout' => 5.0,
			// 'base_uri' => 'https://api.mch.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
		],

		
		'notify_url' => "http://域名/notify.php",
	]
];