<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment\provider\alipay;

use lubaogui\payment\BasePayServer;
use lubaogui\payment\provider\Alipay;

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
    private $alipay = null;

    /**
     * 构造函数 
     *
     * @param array $alipayConfig 配置信息，配置信息重require文件中获得 
     */
    public function __construct() 
    {
       $config = require(dirname(__FILE__) . '/config/alipay.config.php'); 
       $this->alipay = new Alipay($config);
    }

    /**
     * 产生返回给用户浏览器向支付宝服务器提交支付支付请求的html代码
     *
     * @param array $params 请求数组
     */
    public function generateUserRequestHtml($trade) 
    {
        $alipaySubmit = new Alipay($this->_config);
        $requestHtml = $alipaySubmit->buildRequestForm($params, 'get', 'confirm');
        return $requestHtml;
    }

    /**
     * 验证支付宝的服务器返回
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function processNotify() 
    {
        if ($this->alipay->verifyReturn())
        {
            return call_user_func($this->handlers['paySuccessHanlder'], $data);
        }
        else {
            call_user_func($this->handlers['payFailHandler']);
            return false;
        }
    }

    /**
     * 获取支付的支付状态 
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
