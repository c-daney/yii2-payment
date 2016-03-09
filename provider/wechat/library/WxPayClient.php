<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
use yii/base/Model;
 
/**
 * @file WxPayClient.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WxPayClient extends Model 
{
    const URL_WXPAY_UNIFIED_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    const URL_WXPAY_SHORTURL = 'https://api.mch.weixin.qq.com/tools/shorturl';
    const URL_WXPAY_ORDER_QUERY = 'https://api.mch.weixin.qq.com/pay/orderquery';
    const URL_WXPAY_ORDER_CLOSE = 'https://api.mch.weixin.qq.com/pay/closeorder';
    const URL_WXPAY_ORDER_CLOSE = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

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
        $payOrder->setSign();
        $xml = $payOrder->toXml();
        $response = new WxPayResponse($this->postXmlToWechatServer($xml, self::URL_WXPAY_UNIFIED_ORDER));
        return $response;
    }

    /**
     * @brief 查询订单信息
     *
     * @return    
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 16:59:54
    **/
    public static function queryOrder($payOrder) {
        $payOrder->setSign();
        $xml = $payOrder->toXml();
        $response = new WxPayResponse($this->postXmlToWechatServer($xml, self::URL_WXPAY_ORDER_QUERY));
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
        $payOrder->setSign();
        $xml = $payOrder->toXml();
        $response = new WxPayResponse($this->postXmlToWechatServer($xml, self::URL_WXPAY_ORDER_CLOSE));
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
        $payOrder->setSign();
        $xml = $payOrder->toXml();
        $response = new WxPayResponse($this->postXmlToWechatServer($xml, self::URL_WXPAY_SHORTURL));
        return $response;
    }


    /**
     * @brief 向服务器返回成功消息
     *
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/08 10:45:10
    **/
    public static function replyNotifySuccess() {

    }

    /**
     * @brief 向服务器返回失败消息
     *
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/08 10:45:25
    **/
    public static function replyNotifyFailure() {

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

    }

}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
