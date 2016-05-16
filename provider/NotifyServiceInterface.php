<?php

namespace lubaogui\payment\provider;

interface NotifyServiceInterface 
{

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
