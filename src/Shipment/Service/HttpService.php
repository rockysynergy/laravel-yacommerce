<?php

namespace Orq\Laravel\YaCommerce\Shipment\Service;

class HttpService
{
    public function kd100SubscribeTracking(array $data): array
    {
        //参数设置
        $key = config('shiptracking.kd100.key');                            //客户授权key
        $param = array(
            'number' => $data['shipnumber'],    //快递单号
            'key' => $key,                    //客户授权key
            'parameters' => array(
                'callbackurl' => config('shiptracking.kd100.callbackurl'),        //回调地址
                'salt' => config('shiptracking.kd100.salt'),                //加密串
                'resultv2' => '2',            //行政区域解析
                'autoCom' => '0',            //单号智能识别
                'phone' => $data['phone']                //手机号
            )
        );

        //请求参数
        $post_data = array();
        $post_data["schema"] = 'json';
        $post_data["param"] = json_encode($param);

        $url = 'http://poll.kuaidi100.com/poll';    //订阅请求地址
        $params = "";
        foreach ($post_data as $k => $v) {
            $params .= "$k=" . urlencode($v) . "&";        //默认UTF-8编码格式
        }
        $post_data = substr($params, 0, -1);

        //发送post请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = str_replace("\"", '"', $result);
        $data = json_decode($data, true);

        return $data;
    }
}
