<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
/**
 * @file WechatPayNotify.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WechatPayNotify extends WechatPayBase {

    $private $_notifyData = [];
    $private $_config = [];

    /**
     * @brief 构造函数，将Notify的内容转换为WechatPayNotify对象
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
            $this->_config['app_id'] = $this->_notifyData['app_id'];
            $this->_config['mch_id'] = $this->_notifyData['mch_id'];
            $this->_config['trade_type'] = $this->_notifyData['trade_type'];
        }
    }

    /**
     * @brief 检查订单的支付状态，该函数会引起远程网络调用，不能放在事物中处理
     *
     * @return  查询交易状态 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/10 10:28:03
    **/
    public function checkPayStatus($out_trade_no = null) {

        $data = [];
        if (empty($out_trade_no)) {
            $data = $this->_notifyData;
        }
        else {
            $data['out_trade_no'] = $out_trade_no;
        }

        if (empty($data['transaction_id']) && empty($data['out_trade_no'])) {
            $this->addError('transaction_id', 'transaction_id参数为必备参数');
            return false;
        }
        else {
            $payOrder = new WechatPayOrder($this->_config);
            $orderResult = $payOrder->queryPayStatus($data);
            $this->_notifyData = $result;
            if ($orderResult['return_code'] !== 'SUCCESS') {
                $this->addError('wechat-pay', $orderResult['return_msg']);
                return false;
            }
            if ($orderResult['result_code'] !=== 'SUCCESS') {
                $this->addError('wechat-pay-error', $orderResult['err_code_des']);
                return false;
            }
            //支付成功
            if ($orderResult['trade_state'] === 'SUCCESS') {
                return true;
            }
            return true;
        }

    }

    /**
     * @brief 处理支付回告，并返回交易id
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/28 08:16:09
    **/
    public function processNotify($handlers) {

        $receivableId = $this->_notifyData['out_trade_no'];
        $receivable = Receivable::findOne($receivableId);

        if (!$receivable) {
            throw new LBUserException('无法找到对应的交易订单', 3, $this->getErrors());
            return false;
        }

        $receivable->status = Receivable::PAY_STATUS_FINISHED;
        if (!$receivable->save()) {
            throw new LBUserException('保存交易信息失败', 3, $this->getErrors());
            return false;
        }

    }

    /**
     * @brief 查询退款状态,目前应用不需要使用退款功能
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 17:45:53
    **/
    public function checkRefundStatus() {
        return true;
    }

    /**
     * @brief 向服务器发送订单已成功处理信号
     *
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/11 10:24:07
    **/
    public function replySuccessToServer() {

        $reply = ['return_code' => 'SUCCESS', 'return_msg' => 'OK'];
        $replyXml = $this->toXml($reply);
        WechatPayClient::replyNotify($replyXml);

    }


    /**
     * @brief 向服务器发送订单处理失败信号
     *
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/11 10:24:12
    **/
    public function replyFailureToServer() {

        $reply = ['return_code' => 'FAIL', 'return_msg' => 'NOT_OK'];
        $replyXml = $this->toXml($reply);
        WechatPayClient::replyNotify($replyXml);

    }

}


/* vim: set et ts=4 sw=4 sts=4 tw=100: */
