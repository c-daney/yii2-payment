<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment\provider\wechat;

use lubaogui\payment\PayServer as BasePayServer;
use lubaogui\payment\provider\wechat\WechatPay;
use lubaogui\payment\provider\wechat\WechatPayNotify;

/**
 * 支付宝服务类，主要用于产生支付宝请求和校验支付宝的服务器返回.
 *
 *
 * Additionally, when attaching an event handler, extra data may be passed
 * and be available via the [[data]] property when the event handler is invoked.
 *
 * @author Lu Baogui <lbaogui@lubanr.com>
 * @since 2.0
 */
class PayServer extends BasePayServer
{

    /**
     * 构造函数 
     *
     * @param array $wechatpayConfig 配置信息，配置信息重require文件中获得 
     */
    public function __construct() 
    {
       $config = require(dirname(__FILE__) . '/config/config.php'); 
    }

    /**
     * @brief 获取实际的支付实例
     *
     * @return Object 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 19:46:27
    **/
    public function getPayService() {
        if (empty($this->_payService)) {
            $this->_payService = new WechatPay($config);
        }
        return $this->_payService;
    }

    /**
     * @brief 获取回告服务实例
     *
     * @return Object 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 19:49:31
    **/
    public function getNotifyService() {
        if (empty($this->_notifyService)) {
            $this->_notifyService = new WechatPayNotify($config);
        }
        return $this->_notifyService;
    }

    /**
     * @brief 产生客户端支付请求的参数列表
     *
     * @return array 参数数组 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/02/26 00:09:02
    **/
    public function generatePayRequestParams($receivable, $appId) {

        //根据订单信息，统一下单
        $orderParams['body'] = 'Mr-Hug产品充值';
        $orderParams['out_trade_no'] = $receivable->id;
        $orderParams['total_fee'] = round($receivable->money, 2) * 100;
        $orderParams['time_start'] = date('YmdHis', $receivable->created_at);
        $orderParams['time_expire'] = date('YmdHis', $receivable->created_at + 3600);
        $orderParams['goods_tag'] = 'Mr-Hug深度旅游服务 充值';
        $orderParams['product_id'] = 1;

        if (empty($this->payServer)) {
            $this->payServer = new WechatPay($appId);
        }

        return $this->payServer->generateUserRequestParams($orderParams);
    }

    /**
     * @brief 处理后台的支付通知消息，通过调用回调函数来处理相关业务逻辑
     *
     * @return array 数组，返回数组 
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 10:49:12
    **/
    public function processNotify($handlers) {

        if (empty($this->notifyServer)) {
            $this->notifyServer = new WechatPayNotify();
        }

    }

}
