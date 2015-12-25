<?php

namespace lubaogui\payment\provider;

use lubaogui\payment\provider\PayServiceInterface;

abstract class BasePayServer implements PayServiceInterface
{
    /*
     * wechatpay 接口类实例
     */
    protected $payServer;
    protected $notifyServer;
    protected $handlers;

    /**
     * $trade 交易流水
     */
    private $trade = [];

    /**
     * @brief  设置回调相关处理方法
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 11:00:21
    **/
    public function setHandlers($handlers) {
        $this->handlers = $handlers;
    }

    /**
     * 产生用于向支付服务器提交的支付请求页面
     *
     * @param array $params 请求数组
     * @return string 返回请求的form
     */
    abstract public function generateUserRequestHtml($receivable); 

    /**
     * 产生用于向支付服务器提交的支付请求页面
     *
     * @param array $params 请求数组
     * @return string 返回请求的form
     */
    abstract public function generateUserScanQRCode($receivable); 

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
    abstract public function processReturn(); 

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
    abstract public function processNotify(); 

}
