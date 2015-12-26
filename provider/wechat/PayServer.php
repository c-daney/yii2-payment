<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment\provider\wechat;

use lubaogui\payment\provider\BasePayServer;
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

    /**
     * 构造函数 
     *
     * @param array $wechatpayConfig 配置信息，配置信息重require文件中获得 
     */
    public function __construct() 
    {
       $config = require(dirname(__FILE__) . '/config/wechatpay.config.php'); 
       $this->payServer = new WechatPay($config);
       $this->wechatNotify = new WechatPayNotify($config);
    }

    /**
     * @brief 产生用于支付的html form,目前对于微信支付，暂时不支持此种方式
     *
     * @return string html form内容 
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 11:28:49
    **/
    public function generateUserRequestHtml($trade) 
    {
        return '';
    }

    /**
     * @brief 产生用于扫描支付的二维码的url地址
     *
     * @return url 用于产生二维码的url地址  
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 11:23:08
    **/
    public function generateUserScanQRCode($receivable) {
        return $this->payServer->generatePayQRCodeUrl($receivable);
    }

    /**
     * 获取支付单号的支付状态 
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function getPayStatus($receivableId) 
    {
        return true;
    }

    /**
     * @brief 处理后台的支付通知消息，通过调用回调函数来处理相关业务逻辑
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 10:49:12
    **/
    public function processNotify() {
        return $this->notifyServer->Handle(false);
    }

    /**
     * @brief 
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 10:49:18
    **/
    public function processReturn() {


    }

}
