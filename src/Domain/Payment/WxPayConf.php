<?php

namespace Orq\Laravel\YaCommerce\Payment;

use Orq\Wxpay\WxPayConfigInterface as WxpayConfigInterface;

class WxPayConf implements WxpayConfigInterface
{
    public function getAppId()
    {
        return config('pay.wx.app_id');
    }

    public function getMchid()
    {
        return config('pay.wx.merchant_id');
    }

    public function getCurlTimeout()
    {
        return config('pay.wx.curl_timeout');
    }

    public function getAppSecret()
    {
        return config('pay.wx.app_secret');
    }

    public function getKey()
    {
        return config('pay.wx.key');
    }

    public function getSslCertPath()
    {
        return config('pay.wx.ssl_cert_path');
    }

    public function getSslKeyPath()
    {
        return config('pay.wx.key_path');
    }

    public function getNotifyUrl()
    {
        return config('pay.wx.notify_url');
    }

    public function getServerIp()
    {
        return config('pay.wx.server_ip');
    }
}
