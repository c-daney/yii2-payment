<?php

namespace lubaogui\payment\provider;

interface PayServiceInterface 
{
    /**
     * 产生用于向支付宝服务器提交的支付请求
     *
     * @param array $params 请求数组
     */
    public function generateRequest($trade); 

    /**
     * 验证支付服务器返回
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function verifyReturn(); 

    /**
     * 获取支付状态
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function getPayStatus(); 

    /**
     * 退款接口
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function refund($trade); 
}
