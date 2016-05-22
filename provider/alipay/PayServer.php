<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment\provider\alipay;

use lubaogui\payment\PayServer as BasePayServer;
use lubaogui\payment\models\Receivable;

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

    /*
     * alipay 接口类实例
     */
    private $_alipay = null;
    private $_alipayNotify = null;

    /**
     * 构造函数 
     *
     * @param array $alipayConfig 配置信息，配置信息重require文件中获得 
     */
    public function __construct() 
    {
       $this->config = require(dirname(__FILE__) . '/config/alipay.config.php'); 
       $this->_alipay = new Alipay($this->config);
    }

    public function getPayService() {
        if (empty($this->_alipay)) {
            $this->_alipay = new Alipay($this->config);
        }
        return $this->_alipay;
    }

    public function getNotifyService() {
        if (empty($this->_alipayNotify)) {
            $this->_alipayNotify = new AlipayNotify($this->config);
        }
        return $this->_alipayNotify;
    }

}
