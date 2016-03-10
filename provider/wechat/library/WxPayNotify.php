<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
/**
 * @file WxPayNotify.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WxPayNotify extends WxPayBase {

    $private $_notifyData = [];

    /**
     * @brief 构造函数，将Notify的内容转换为WxPayNotify对象
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/10 10:39:42
    **/
    public function __construct() {
        $xml = file_get_contents("php://input");
        if ($xml) {
            $this->_notifyData = $this->transferXmlToArray($xml);
        }
    }

    /**
     * @brief 
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/10 10:28:03
    **/
    public function checkTradeStatus($data) {

        if (empty($data['transaction_id']) && empty($data['out_trade_no'])) {
            $this->addError('transaction_id', 'transaction_id参数为必备参数');
            return false;
        }
        else {
            $payOrder = new WxPayOrder();
            $result = $payOrder->query($data);
            $this->_notifyData = $result;
            return true;
        }

    }

}


/* vim: set et ts=4 sw=4 sts=4 tw=100: */
