<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
use yii\base\Model;
 
/**
 * @file WechatPayClient.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WechatPayClient 
{

    const CURL_PROXY_HOST = '';
    const CURL_PROXY_PORT = '';

    const URL_WXPAY_UNIFIED_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const URL_WXPAY_SHORTURL = 'https://api.mch.weixin.qq.com/tools/shorturl';
    const URL_WXPAY_ORDER_QUERY = 'https://api.mch.weixin.qq.com/pay/orderquery';
    const URL_WXPAY_ORDER_CLOSE = 'https://api.mch.weixin.qq.com/pay/closeorder';
    const URL_WXPAY_ORDER_REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * @brief 统一下单
     *
     * @return  
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 17:00:07
     **/
    public static function generateUnifiedOrder($payOrder) {

        $xml = $payOrder->toXml($payOrder->toArray());
        $response = new WechatPayResponse(self::postXmlToWechatServer($xml, self::URL_WXPAY_UNIFIED_ORDER));
        return $response;

    }

    /**
     * @brief 查询订单信息
     *
     * @return   bool|mixed 如果失败，返回false, 如果成功，返回数组信息 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 16:59:54
     **/
    public static function queryOrderPayStatus($payOrder) {

        $xml = $payOrder->toXml($payOrder->attributes());
        $response = new WechatPayResponse(self::postXmlToWechatServer($xml, self::URL_WXPAY_ORDER_QUERY));
        return $response;

    }

    /**
     * @brief 关闭订单
     *
     * @return 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 17:00:24
     **/
    public static function closeOrder($payOrder) {
        $xml = $payOrder->toXml();
        $response = new WechatPayResponse(self::postXmlToWechatServer($xml, self::URL_WXPAY_ORDER_CLOSE));
        return $response;
    }

    /**
     * @brief 生成短链接 
     *
     * @return    
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 12:17:28
     **/
    public static function shorturl($payOrder, $timeout = 6) {
        $xml = $payOrder->toXml();
        $response = new WechatPayResponse(self::postXmlToWechatServer($xml, self::URL_WXPAY_SHORTURL));
        return $response;
    }

    /**
     * @brief 对通知进行回复，该函数是对接口的封装
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 17:27:53
     **/
    protected static function replyNotify($xml) {
        echo $xml;
    }

    /**
     * @brief 向服务器发送请求
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 12:17:28
     **/
    protected static function postXmlToWechatServer($xml, $url, $useCert = false, $timeout = 30) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        if(self::CURL_PROXY_HOST != "0.0.0.0" && self::CURL_PROXY_PORT != 0 ) {
            curl_setopt($ch,CURLOPT_PROXY, WechatPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch,CURLOPT_PROXYPORT, WechatPayConfig::CURL_PROXY_PORT);
        }

        curl_setopt($ch,CURLOPT_URL, $url); 
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE); 
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        curl_setopt($ch, CURLOPT_HEADER, FALSE); 
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, WechatPayConfig::SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, WechatPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else { 
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WechatPayException("curl出错，错误码:$error");
        }

    }

}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
