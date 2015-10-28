<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment;

/**
 * 支付组件接口
 *
 * @author Baogui Lu (lbaogui@lubanr.com)
 * @version since 2.0
 */
class Payment 
{
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
    private $_payment;

    /**
     * provider 支付提供商
     */
    private $_provider;

    /**
     * 构造函数
     * @param provider string 支付供应商名称
     */
    public function __construct($provider) 
    {
        $this->_provider = $provider;
    }

    /**
     * 支付方法对应的支付服务类
     */
    public $paymentMap = [
        'alipay' => 'lubaogui\payment\provider\alipay\Payment',
        'wechat' => 'lubaogui\payment\provider\wechatpay\Payment',
    ];

    /*
     * 获取实际的支付实例,类支持chain
     * 
     * @return object 支付实例
     */
    public function getPayment() 
    {
        if ($this->_payment !== null) {
            return $this->_payment;
        }
        else {
            $provider = $this->getProvider();
            if (isset($this->paymentMap[$provider])) {
                $config = !is_array($this->paymentMap[$provider]) ? 
                    ['class' => $this->paymentMap[$provider]] : 
                    this->paymentMap[$provider];
                return Yii::createObject($config);
            }
            else {
                throw new Exception('payment your specified is not supported now!');
            }
        }
    }

    /*
     * 获取支付供应商支付名称 
     * 
     * @return string 支付供应商名称
     */
    public function getProvider() 
    {
        return $this->_provider;
    }

    /*
     * 跳转到支付页面
     * 
     * @return string 支付block内容页面,通常是自动的js跳转
     */
    public gotoPay() {
        return $this->_payment->generateRequest()；
    }

    /*
     * 跳转到支付页面
     * 
     * @return string 支付供应商名称
     */
    public verifyReturn() {
        return $this->_payment->verifyReturn();
    }
}
