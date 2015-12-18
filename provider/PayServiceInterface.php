<?php

namespace lubaogui\payment\provider;

interface PayServiceInterface 
{
    /**
     * 产生用于向支付宝服务器提交的支付请求
     *
     * @param array $params 请求数组
     */
    public function generateUserRequestHtml($trade); 

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


    /**
     * @brief 设置支付成功的回调函数
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/18 23:01:46
    **/
    public function setPaySucceededHandler($paySucceededHandler);

    /**
     * @brief 设置支付失败时的回调函数
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/18 23:02:09
    **/
    public function setPayFailedHandler($payFailedHandler);
}
