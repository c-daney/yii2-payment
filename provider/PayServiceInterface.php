<?php

namespace lubaogui\payment\provider;

interface PayServiceInterface 
{
    /**
     * 产生用于向支付服务器提交的支付请求页面
     *
     * @param array $params 请求数组
     * @return string 返回请求的form
     */
    public function generateUserRequestHtml($receivable); 

    /**
     * 产生用于向支付服务器提交的支付请求页面
     *
     * @param array $params 请求数组
     * @return string 返回请求的form
     */
    public function generateUserScanQRCode($receivable); 

    /**
     * @brief 处理支付服务器的前端返回
     *
     * @return  abstract public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 11:17:28
    **/
    public function processReturn(); 

    /**
     * @brief 处理支付服务器的后台通知
     *
     * @return  abstract public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 11:16:50
    **/
    public function processNotify($handlers); 


}
