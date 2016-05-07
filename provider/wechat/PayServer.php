<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment\provider\wechat;

use Yii;
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
    private $_config = []; 
    public $app_id;

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
            $configs = require(dirname(__FILE__) . '/config/config.php'); 
            if ($this->app_id) {
                $this->_config = $configs['apps'][$this->app_id];
            }
            else {
                $this->_config = $configs['apps'][$configs['default_app_id']];
            }

            $this->_payService = new WechatPay($this->_config);
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
            $this->_notifyService = new WechatPayNotify();
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
    public function generatePayRequestParams($receivable) {

        //根据订单信息，统一下单
        $orderParams['body'] = 'Mr-Hug产品充值';
        $orderParams['out_trade_no'] = $receivable->id;
        //$orderParams['total_fee'] = round($receivable->money, 2) * 100;
        $orderParams['total_fee'] = round($receivable->money, 2);
        $orderParams['time_start'] = date('YmdHis', $receivable->created_at);
        $orderParams['time_expire'] = date('YmdHis', $receivable->created_at + 3600);
        $orderParams['goods_tag'] = 'Mr-Hug深度旅游服务 充值';
        $orderParams['product_id'] = 1;

        $payService = $this->getPayService();
        return $payService->generatePayRequestParams($orderParams);
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
        return $this->getNotifyService()->checkPayStatus($out_trade_no);
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
        return $this->getNotifyService()->processNotify($handlers);
    }

}
