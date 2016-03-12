<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
/**
 * @file WechatPayOrder.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:03:20
 * @version $Revision$
 * @brief
 *
 **/

class WechatPayOrder extends WechatPayBase 
{

    public $trade_type;
    public $out_trade_no;
    public $body;
    public $detail;
    public $total_fee;
    public $once_str;
    public $fee_type;

    /**
     * @brief 场景规则列表
     *
     * @return  array 规则列表 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 14:50:39
    **/
    public function rules() {
        return [
            'unifiedOrder' => [

            ],
            'query' => [

            ],
            'close' => [

            ],
        ];
    }

    /**
     * @brief 统一下单, 网络相关调用不得加入到事务处理过程当中
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 11:53:24
    **/
    public function generateUnifiedOrder($orderParams) {

        $this->scenario = 'unifiedOrder';
        $this->load($orderParams);

        //签名
        $this->setSign();
        
        //统一下单的结果
        $wxResponse = WechatPayClient::generateUnifiedOrder($this);
        return $wxResponse;

    }

    /**
     * @brief 查询订单 
     *
     * @param int transaction_id 订单交易id
     * @return bool 是否支付成功 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 11:53:48
    **/
    public function queryPayStatus($orderParams) {

        $this->scenario = 'query';
        $this->load($orderParams);
        $this->setSign();
        return WechatPayClient::queryOrderPayStatus($this);

    }

    /**
     * @brief 关闭订单
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 15:15:25
    **/
    public function close($orderParams) {

    }

}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
