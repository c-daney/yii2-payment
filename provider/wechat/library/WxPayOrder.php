<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
/**
 * @file WxPayOrder.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:03:20
 * @version $Revision$
 * @brief
 *
 **/

class WxPayOrder extends WxPayBase {

    private $_config;

    /**
     * @brief 将订单信息转换成xml格式
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/04 17:38:19
    **/
    public function toXml() {


    }

    /**
     * @brief 统一下单
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 11:53:24
    **/
    public function unifiedOrder() {


    }

    /**
     * @brief 订单退款
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 11:53:48
    **/
    public function refundOrder() {

    }
}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
