<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment;

/**
 * 支付组件接口,暴露给外部的接口,组件虽有models等，但不对外提供功能，对外只提供几个接口函数, 收款和提现
 * 支付宝回告通知可调用本扩展来完成业务逻辑，为了解耦合，订单相关的处理逻辑最好以回调函数方式处理，这样做可以最大化复用
 * 代码
 * @author Baogui Lu (lbaogui@lubanr.com)
 * @version since 2.0
 */
class Payment 
{

    /**
     *  消息定义
     */
    const EVENT_PAY_SUCCEEDED = 'paySucceeded';
    const EVENT_PAY_FAILED = 'payFailed';
    const EVENT_REFUND_SUCCEEDED = 'refundSucceeded';
    const EVENT_REFUND_FAILED = 'refundFailed';

    /**
     * 支付状态定义
     */
    const PAY_STATUS_CREATED = 0;
    const PAY_STATUS_FINISHED = 10;
    const PAY_STATUS_CLOSED =   20;
    const PAY_STATUS_SUCCEEDED = 30;
    const PAY_STATUS_REFUNDED = 40;

    /**
     *  支付服务实例
     */
    private $payServer;

    /**
     * provider 支付提供商名称
     */
    private $provider;

    /*
     * 支付交易内容
     */
    public $trans = null;

    /**
     * 构造函数
     * @param provider string 支付供应商名称
     */
    public function __construct($provider) 
    {
        $this->provider = $provider;
        if (isset($this->payServerMap[$this->provider])) {
            $config = !is_array($this->payServerMap[$this->provider]) ? 
                ['class' => $this->payServerMap[$this->provider]] : 
                this->payServerMap[$this->provider];
            $this->payServer = Yii::createObject($config);
            return $this->payServer;
        }
        else {
            throw new Exception('payment server your specified ' . $this->provider . ' is not supported now!');
        }
    }

    /**
     * 支付方法对应的支付服务类
     */
    static private $payServerMap = [
        'alipay' => 'lubaogui\payment\provider\alipay\PayServer',
        'wechat' => 'lubaogui\payment\provider\wechatpay\PayServer',
    ];

    /*
     * 获取实际的支付实例,类支持chain操作
     * 
     * @return object 支付实例
     */
    public function getPayServer() 
    {
        return $this->payServer;
    }

    /*
     * 跳转到第三方支付平台支付页面
     * 
     * @return string 支付block内容页面,通常是自动的js跳转
     */
    public gotoPayPage() {
        if (empty($this->trans)) {
            throw new Exception('trans info must be set!');
        }

        return $this->payServer->generateUserRequestHtml($this->trans)；
    }

    /*
     * 验证支付回告是否为支付平台所发
     * 
     * @return bool 是否是真正的回告
     */
    public verifyReturn() {
        return $this->payServer->verifyReturn();
    }

    /*
     * 获取支付供应商支付名称 
     * 
     * @return string 支付供应商名称
     */
    public function getProvider() 
    {
        return $this->provider;
    }

    /*
     *  支付成功之后的处理方法
     * 
     */
    public function processSuccess() 
    {
        $this->trigger(self::EVENT_PAY_SUCCEEDED);
    }

    /*
     *  支付失败之后的处理方法   
     * 
     */
    public function processFailure() 
    {
        $this->trigger(self::EVENT_PAY_FAILED);
    }

}
