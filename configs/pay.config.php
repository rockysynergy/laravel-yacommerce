<?php

return [
    'wx' => [
        'app_id' => env('WX_MP_APPID'),
        'app_secret' => env('WX_MP_APPSECRET'),
        'merchant_id' => env('WX_MCHID'),
        'curl_timeout' => env('WX_CURL_TIMEOUT', '30'),
        'key' => env('WX_KEY'),
        'ssl_cert_path' => env('WX_SSL_CERT_PATH'),
        'key_path' => env('KEY_PATH'),
        'notify_url' => 'https://wqb.fs007.com.cn/fe/wxpay/notify',
        'server_ip' => '47.106.94.36',
    ],
    'pay_method' => [
        '1' => '微信',
        '2' => '现金',
        '3' => '支付宝',
    ],
    'pay_status' => [
        '1' => '等待支付',
        '2' => '完成支付',
    ],
];
