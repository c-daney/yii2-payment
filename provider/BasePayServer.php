<?php

namespace lubaogui\payment\provider;

abstract class PaymentService extends PaymentServiceInterface
{
    /**
     * $trade 交易流水
     */

    private $_trade = [];

    /**
     * 产生用于向支付服务器提交的支付请求页面
     *
     * @param array $params 请求数组
     * @return string 返回请求的form
     */
    abstract public function generateRequest($order); 

    /**
     * 验证支付服务器返回
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    abstract public function verifyReturn(); 

    /**
     * 退款接口
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    abstract public function refund($order);
}
