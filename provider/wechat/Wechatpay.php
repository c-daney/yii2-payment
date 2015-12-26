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

class WechatPay {

    // 配置信息在实例化时从配置文件读入，配置文件需要放在该文件同目录下
    private $config = [ 
        'notify_url'=>'',
        'trade_type'=>'NATIVE',
        'qrcode_url'=>'',
    ];

    private $payOrder = null;

    /**
     * @brief 构造函数，做的工作主要是将配置文件和默认配置进行merge,同时设置notify所需要的成功和失败的回调函数
     *
     * @return  function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/17 20:56:45
    **/
    function __construct($isMobile = false){
        $this->config = array_merge($this->config, require(dirname(__FILE__) . '/config/config.php'));
        $this->payOrder = new \WxPayUnifiedOrder();
        if (empty($this->payOrder)) {
            return false;
        }
        $this->payOrder->SetNotify_url($this->config['notify_url']);
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
    public function generatePayQRCodeUrl($receivable) {

        $currentTime = date("YmdHis");
        $this->payOrder->setBody('Mr-Hug产品充值购买');
        $this->payOrder->setAttach('用于购买Mr-Hug服务');
        $this->payOrder->SetOut_trade_no($receivable->id);
        $this->payOrder->SetTotal_fee($receivable->money);
        $this->payOrder->SetTime_start(date('YmdHis', $receivable->created_at));
        $this->payOrder->SetTime_expire(date('YmdHis', $receivable->created_at+1800));
        $this->payOrder->SetGoods_tag('服务，充值');
        $this->payOrder->SetTrade_type('充值服务');
        $this->payOrder->SetProduct_id(1);
        $result = $this->notify->GetPayUrl($this->payOrder);
        $payUrl = $result['code_url'];

        //payQRCodeUrl为的动态生成二维码的在线地址
        $payQRCodeUrl = $this->config['qrcode_url'] . urlencode($payUrl);

    }

}
/* vim: set et ts=4 sw=4 sts=4 tw=100: */
