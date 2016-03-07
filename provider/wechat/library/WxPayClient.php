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
    public function generateUnifiedOrder($payOrder) {
        $payOrder->setSign();
        $xml = $payOrder->toXml();
        $response = new WxPayResponse($this->postXmlToWechatServer($xml, $url));
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
    public function queryOrder($payOrder) {

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
    public function closeOrder($payOrder) {

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
    public function shorturl($payOrder, $timeout = 6) {

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
    public function replyNotify($xml) {

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
    protected function postXmlToWechatServer($xml, $url, $useCert = false, $timeout = 30) {

    }

}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
