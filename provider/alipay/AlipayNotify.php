<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Lubanr.com All Rights Reserved
 *
 **************************************************************************/

namespace lubaogui\payment\provider\alipay;

use Yii;
use  lubaogui\payment\provider\alipay\library\AlipayBase;
use lubaogui\payment\models\Receivable;
use lubaogui\common\exceptions\LBUserException;

/**
 * @file Alipay.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2015/11/10 16:04:11
 * @version $Revision$
 * @brief
 *
 **/


class AlipayNotify extends AlipayBase {

    private $_notifyData;

    function __construct($config, $isMobile = false){
        $this->config = $config;
        $this->isMobile = $isMobile;
        if ($isMobile) {
            $this->service = $this->serviceWap;
            $this->alipayGateway = $this->alipayGatewayMobile;
        }
        else {

        }

        $this->_notifyData = $_POST;
        if (empty($this->_notifyData)) {
            Yii::warning('回告的内容数据为空', __METHOD__);
            return false;
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

        if (! $this->verifyNotify($this->_notifyData)) {
            Yii::warning('支付宝回调结果真实性check failed!');
            return false;
        }
        else {
            //此处需要判断支付的状态，只有支付成功的状态才会返回true
            $tradeStatus = trim($this->_notifyData['trade_status']);
            Yii::warning('订单状态为:' . $tradeStatus, __METHOD__);
            if ($tradeStatus === 'TRADE_FINISHED' || $tradeStatus === 'TRADE_SUCCESS') {
                Yii::warning('订单已支付成功', __METHOD__);
                return true;
            }
            else {
                Yii::warning('订单回告显示订单未支付成功', __METHOD__);
                return false;
            }
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
        else {
            //call callback function
            if (call_user_func($handlers['paySuccessHandler'], $receivable->trans_id)) {
                return $receivable->trans_id;
            }
            else {
                Yii::warning(__METHOD__ . ' callback failed');
                return false;
            }
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
    public function getReceivable($receivableId = null) {

        if (! $receivableId) {
            $receivableId = $this->_notifyData['out_trade_no'];
        }
        $receivable = Receivable::findOne($receivableId);
        if (! $receivable) {
            Yii::error('无法找到对应的交易订单');
            return false;
        } 
        else { 
            return $receivable;
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
        return "success";
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
        return 'failure';
    }

}
/* vim: set et ts=4 sw=4 sts=4 tw=100: */
