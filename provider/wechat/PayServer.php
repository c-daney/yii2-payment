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
 * 微信支付服务类，主要用于产生请求和校验服务器返回.
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
    private $_configs = [];


    public function __construct() {
        $this->_configs = require(__DIR__ . '/config/config.php'); 
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
            if ($this->trade_type) {
                $this->_config = $this->_configs['apps'][$this->trade_type];
            }
            else {
                $this->_config = $this->_configs['apps'][$this->_configs['default_trade_type']];
            }
            $this->_config['trade_type'] = $this->trade_type;
            $this->_payService = new WechatPay($this->_config);
        }
        Yii::warning($this->_config, __METHOD__);
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
            $this->_notifyService = new WechatPayNotify($this->_configs);
        }
        return $this->_notifyService;
    }



}
