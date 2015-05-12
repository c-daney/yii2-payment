<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

require_once(dirname(__FILE__) . 'lib/alipay.config.php'); 
require_once(dirname(__FILE__) . 'lib/alipay_submit.class.php'); 

namespace lubaogui\payment\alipay;

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
class Alipay implements PayServiceInterface
{
    /**
     *  支付宝网关地址
     */
    private $gateway = ''; 

    /**
     *  支付宝相关配置信息
     */
    private $config;

    /**
     * 构造函数 
     *
     * @param array $alipay_config 配置信息，配置信息重require文件中获得 
     */
    public function __construct($alipay_config) {
       $this->config = $alipay_config; 
    }

    /**
     * 产生用于向支付宝服务器提交的支付请求
     *
     * @param array $params 请求数组
     */
    public function generateWebSubmitRequest($params) {
        $alipaySubmit = new \AlipaySubmit($this->config);
        $requestHtml = $alipaySubmit->buildRequestFomr($params, 'get', 'confirm');
        return $requestHtml;
    }

    /**
     * 验证支付宝的服务器返回
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function verifyReturn() {

    }
}
