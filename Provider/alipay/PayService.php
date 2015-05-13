<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

require_once(dirname(__FILE__) . 'lib/alipay_submit.class.php'); 
require_once(dirname(__FILE__) . 'lib/alipay_notify.class.php'); 

namespace lubaogui\payment\provider\alipay;

use lubaogui\payment\provider\PayServiceInterface;

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
class PayService implements PayServiceInterface
{
    /**
     *  支付宝相关配置信息
     */
    private $_config;

    /**
     * 构造函数 
     *
     * @param array $alipayConfig 配置信息，配置信息重require文件中获得 
     */
    public function __construct() 
    {
       $this->_config = require(dirname(__FILE__) . 'lib/alipay.config.php'); 
    }

    /**
     * 产生用于向支付宝服务器提交的支付请求
     *
     * @param array $params 请求数组
     */
    public function generateRequest($params) 
    {
        $alipaySubmit = new \AlipaySubmit($this->_config);
        $requestHtml = $alipaySubmit->buildRequestForm($params, 'get', 'confirm');
        return $requestHtml;
    }

    /**
     * 验证支付宝的服务器返回
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function verifyReturn() 
    {
        $notify = new \AlipayNotify($this->_config);
        if ($notify->verifyNotify())
        {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * 支付请求的交易状态
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function getPayStatus() 
    {
        $returnStatus = $_POST['trade_status'];
        $payStatus = Payment::PAY_STATUS_CREATE;
        switch $tradeStatus {
            case 'WAIT_BUYER_PAY': {
                $payStatus = Payment::PAY_STATUS_CREATED;               
                break;
            }
            case 'TRADE_FINISHED': {
                $payStatus = Payment::PAY_STATUS_FINISHED;               
                break;
            }
            case 'TRADE_SUCCESS': {
                $payStatus = Payment::PAY_STATUS_SUCCEEDED;               
                break;
            }
            case 'TRADE_CLOSED': {
                $payStatus = Payment::PAY_STATUS_CLOSED;               
                break;
            }
            default: break;
        }
    }
}
