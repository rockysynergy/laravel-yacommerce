<?php

namespace Orq\Laravel\YaCommerce\Payment;

use Orq\Wxpay\Utility;
use Orq\Wxpay\UnifiedOrder;

class WxPay
{
    /**
     * Communicate with WeiXin server to get prepayid
     */
    public static function makeUnifiedOrder(array $order):string
    {
        $wxPayConf = new WxPayConf();
        $uOrder = new UnifiedOrder($wxPayConf);
        $uOrder->setParameter("openid", $order['openid']);//用户openid
		$uOrder->setParameter("body", $order['body']);//商品描述
		//自定义订单号，此处仅作举例
		$timeStamp = time();
		$uOrder->setParameter("out_trade_no", $order['out_trade_no']);//商户订单号
		$uOrder->setParameter("total_fee", $order['total_fee']);//总金额
		$uOrder->setParameter("notify_url",$wxPayConf->getNotifyUrl());//通知地址
        $uOrder->setParameter("trade_type","JSAPI");//交易类型;

        return $uOrder->getPrepayId();
    }

    /**
     * assemble the payload for wx.requestPay
     */
    public static function assemblePayload(string $prepayId):array
    {
        $wxPayConf = new WxPayConf();
        $param = [
            'appId'=>$wxPayConf->getAppId(),
            'timeStamp'=>time(),
            'nonceStr'=>Utility::createNoncestr(),
            'package'=>'prepay_id='.$prepayId,
            'signType'=>'MD5'
        ];
        $paySign = Utility::getSign($param, $wxPayConf);
        $param['paySign']=$paySign;
        return $param;
    }
}
