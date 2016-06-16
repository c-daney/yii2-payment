<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
namespace lubaogui\payment\provider\wechat;

/**
 * @file WechatPay.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2015/11/10 16:04:11
 * @version $Revision$
 * @brief
 *
 **/

use Yii;
use yii\base\Model;
use yii\base\Exception;
use lubaogui\payment\provider\wechat\library\WechatPayOrder;;

class WechatPay extends Model {

    // 配置信息在实例化时从配置文件读入，配置文件需要放在该文件同目录下
    private $_config = [];

    private $_payOrder = null;

    /**
     * @brief 构造函数，做的工作主要是将配置文件和默认配置进行merge,同时设置notify所需要的成功和失败的回调函数,
     * 微信的pc端支付和移动端支付不一直，因此构建时候需要提供参数，是否是移动端
     *
     * @return  function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/17 20:56:45
    **/
    function __construct($config = null){
        $this->_config = $config;
        $this->_payOrder = new WechatPayOrder($this->_config);
        if (empty($this->_payOrder)) {
            return false;
        }
    }

    /**
     * @brief 产生用于微信支付的参数列表(供客户端或者网页使用)
     *
     * @return array 支付参数列表   
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/02/26 00:12:06
    **/
    public function generatePayRequestParams($receivable) {

        Yii::error("generate pay for according to receivable", __METHOD__);
        //订单参数
        $orderParams['body'] = 'Mr-Hug产品充值';
        $orderParams['out_trade_no'] = $receivable->id;
        $orderParams['total_fee'] = round($receivable->money*100);
        $orderParams['time_start'] = date('YmdHis', $receivable->created_at);
        $orderParams['time_expire'] = date('YmdHis', $receivable->created_at + 3600);
        $orderParams['goods_tag'] = 'Mr-Hug深度旅游服务 充值';
        $orderParams['product_id'] = 1;

        //通用参数
        $orderParams['notify_url'] = $this->_config['notify_url'];
        $orderParams['trade_type'] = $this->_config['trade_type'];
        Yii::error("订单参数为:");
        Yii::error($orderParams);

        $response = $this->generateUnifiedOrder($orderParams);
        if ($response == false) {
            $this->addError(__METHOD__, 'response content is false!');
            return false;
        }
        $resultData = $response->getAttributes();
        Yii::error("response content of UnifyOrder:");
        Yii::error($resultData);

        //根据不同的交易类型，产出不同的输出
        switch ($orderParams['trade_type']) {
        case 'NATIVE': {
            $codeUrl = $resultData['code_url'];
            $payQRCodeUrl = $this->_config['qrcode_gen_url'] . $codeUrl;
            return $payQRCodeUrl;
        }
        case 'APP': {
            $clientOrderParams = [];
            $clientOrderParams['appid'] = $resultData['appid'];
            $clientOrderParams['noncestr'] = $resultData['nonce_str'];
            $clientOrderParams['partnerid'] = $resultData['mch_id'];
            $clientOrderParams['prepayid'] = $resultData['prepay_id'];
            $clientOrderParams['timestamp'] = $receivable->created_at;
            $clientOrderParams['package'] = 'Sign=WXPay';

            $wxPayOrder = new WechatPayOrder($this->_config);
            $wxPayOrder->setScenario('generateAppForm');
            $wxPayOrder -> load($clientOrderParams, '');
            Yii::error($clientOrderParams, __METHOD__);
            Yii::error($wxPayOrder, __METHOD__);
            $wxPayOrder -> setSign();
            Yii::error("签名之后的订单参数为:");
            Yii::error($wxPayOrder->getAttributes(), __METHOD__);

            return $wxPayOrder->getAttributes();
        }
        default:break;
        }

    }

    /**
     * @brief 统一下单接口
     *
     * @return  protected function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/12 16:31:32
    **/
    protected function generateUnifiedOrder($orderParams) {

        $wxPayOrder = new WechatPayOrder($this->_config);
        $response = $wxPayOrder -> generateUnifiedOrder($orderParams);
        $unifiedOrderData = $response->getAttributes();
        if ($unifiedOrderData['return_code'] !== 'SUCCESS') {
            //$this->addError('wechat-pay-unified-order', $unifiedOrderData['return_msg']);
            $this->addError('wechat-pay-unified-order', $unifiedOrderData);
            $this->addError(__METHOD__, $response);
            return false;
        }
        if ($unifiedOrderData['result_code'] !== 'SUCCESS') {
            //$this->addError('wechat-pay-unified-order', $unifiedOrderData['err_code_des']);
            $this->addError('wechat-pay-unified-order', $unifiedOrderData);
            return false;
        }

        return $response;

    }

}
/* vim: set et ts=4 sw=4 sts=4 tw=100: */
