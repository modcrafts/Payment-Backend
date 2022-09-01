<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Config;
use EasyWeChat\Pay\Application;

Factory::setOptions(getAlipayOptions($config));

$wechat = new Application($config['wechat']);

function getAlipayOptions($config)
{
    
    //沙盒模式
    $dev_mode = false;

    $options = new Config();
    $options->protocol = 'https';
    $options->gatewayHost = !$dev_mode ? /*应用地址*/$config['alipay']['gatewayHost'] : /*沙盒地址*/$config['alipay']['gatewayHostDev'];
    $options->signType = $config['alipay']['sign_type'];
    
    $options->appId = !$dev_mode ? /*应用密钥*/$config['alipay']['app_id'] : /*沙盒密钥*/$config['alipay']['app_id_dev'];
    
    // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
    $options->merchantPrivateKey = $config['alipay']['merchant_private_key'];
    /*
    $options->alipayCertPath = '<-- 请填写您的支付宝公钥证书文件路径，例如：/foo/alipayCertPublicKey_RSA2.crt -->';
    $options->alipayRootCertPath = '<-- 请填写您的支付宝根证书文件路径，例如：/foo/alipayRootCert.crt" -->';
    $options->merchantCertPath = '<-- 请填写您的应用公钥证书文件路径，例如：/foo/appCertPublicKey_2019051064521003.crt -->';
    */
    //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
    $options->alipayPublicKey = !$dev_mode ? $config['alipay']['alipay_public_key'] : $config['alipay']['alipay_public_key_dev'];

    //可设置异步通知接收服务地址（可选）
    $options->notifyUrl = $config['alipay']['notify_url'];
    
    //可设置AES密钥，调用AES加解密相关接口时需要（可选）
    $options->encryptKey = $config['alipay']['encrypt_key'];

    return $options;
}


