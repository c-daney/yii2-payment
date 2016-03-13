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

require_once('lib/WxPay.Data.php');
require_once('lib/WxPay.NativePay.php');
require_once('lib/WxPay.Config.php');

use yii\base\Exception;
use Yii;

class WechatPay {

    // 配置信息在实例化时从配置文件读入，配置文件需要放在该文件同目录下
    private $config = [ 
        'notify_url'=>'',
        'trade_type'=>'NATIVE',
        'qrcode_gen_url'=>'',
    ];

    private $payOrder = null;
    private $notify = null;

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
    function __construct(){
        $this->config = array_merge($this->config, require(dirname(__FILE__) . '/config/config.php'));
        $this->payOrder = new WechatPayOrder();
        if (empty($this->payOrder)) {
            return false;
        }
    }

    /**
     * @brief 产生支付二维码的图片地址
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/17 20:52:43
    **/
    public function generateUserScanQRCode($receivable) {

        $orderParams['body'] = 'Mr-Hug产品充值';
        $orderParams['out_trade_no'] = $receivable->id;
        $orderParams['total_fee'] = round($receivable->money, 2) * 100;
        $orderParams['time_start'] = date('YmdHis', $receivable->created_at);
        $orderParams['time_expire'] = date('YmdHis', $receivable->created_at + 3600);
        $orderParams['goods_tag'] = 'Mr-Hug深度旅游服务 充值';
        $orderParams['product_id'] = 1;
        $orderParams['notify_url'] = 1;

        $response = $this->generateUnifiedOrder($orderParams);
        $resultData = $response->getAttributes();

        if ($resultData['return_code'] !=== 'SUCCESS') {
            return false;
        }
        if ($resultData['result_code'] !=== 'SUCCESS') {
            return false;
        }

        $codeUrl = $resultData['code_url'];
        $payQRCodeUrl = $this->config['qrcode_gen_url'] . $codeUrl;
        return $payQRCodeUrl;

    }

    /**
     * @brief 产生用于微信支付的参数列表
     *
     * @return array 支付参数列表   
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/02/26 00:12:06
    **/
    public function generateUserRequestParams($receivable) {

        $orderParams['body'] = 'Mr-Hug产品充值';
        $orderParams['out_trade_no'] = $receivable->id;
        $orderParams['total_fee'] = round($receivable->money, 2) * 100;
        $orderParams['time_start'] = date('YmdHis', $receivable->created_at);
        $orderParams['time_expire'] = date('YmdHis', $receivable->created_at + 3600);
        $orderParams['goods_tag'] = 'Mr-Hug深度旅游服务 充值';
        $orderParams['product_id'] = 1;
        $orderParams['notify_url'] = 1;

        $response = $this->generateUnifiedOrder($orderParams);
        $resultData = $response->getAttributes();

        if ($resultData['return_code'] !=== 'SUCCESS') {
            return false;
        }
        if ($resultData['result_code'] !=== 'SUCCESS') {
            return false;
        }

        $clientOrderParams = [];
        $clientOrderParams['appid'] = $resultData['apid'];
        $clientOrderParams['noncestr'] = $resultData['nonce_str'];
        $clientOrderParams['partnerid'] = $resultData['mch_id'];
        $clientOrderParams['prepayid'] = $resultData['prepay_id'];
        $clientOrderParams['timestamp'] = $receivable->created_at;
        $clientOrderParams['package'] = 'Sign=WXPay';

        $wxPayOrder = new WechatPayOrder();
        $wxPayOrder -> load($clientOrderParams);
        $wxPayOrder -> setSign();

        return $wxPayOrder->getAttributes();

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
    protected function unifiedOrder($orderParams) {

        $wxPayOrder = new WechatPayOrder();
        $response = $wxPayOrder -> generateUnifiedOrder($orderParams);
        $unifiedOrderData = $response->getAttributes();
        if ($unifiedOrderData['return_code'] !== 'SUCCESS') {
            $this->addError('wechat-pay-unified-order', $unifiedOrderData['return_msg']);
            return false;
        }
        if ($unifiedOrderData['result_code'] !=== 'SUCCESS') {
            $this->addError('wechat-pay-unified-order', $unifiedOrderData['err_code_des']);
            return false;
        }
        //支付成功
        if ($unifiedOrderData['trade_state'] === 'SUCCESS') {
            return $response->getAttributes();
        }

    }

}
/* vim: set et ts=4 sw=4 sts=4 tw=100: */
