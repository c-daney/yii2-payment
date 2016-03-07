<?php
/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
/**
 * @file WxPayOrder.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:03:20
 * @version $Revision$
 * @brief
 *
 **/

class WxPayOrder extends WxPayBase {

    const WECHAT_PAY_SERVER = '';
    const WECHAT_SHORTURL_SERVER = '';
    const WECHAT_CLOSE_SERVER = '';
    const WECHAT_CLOSE_SERVER = '';

    public $trade_type;
    public $out_trade_no;
    public $body;
    public $detail;
    public $total_fee;
    public $once_str;
    public $fee_type;

    public $sign;
    public $appId;
    public $mchId;
    public $key;

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
     * @brief 统一下单
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 11:53:24
    **/
    public function unifiedOrder($orderParams) {

        $this->scenario = 'unifiedOrder';
        $this->load($orderParams);
        //签名
        $this->setSign();
        $xmlString = $this->toXml($orderParams);
        
        //统一下单的结果
        $wxResponse = new WxPayResponse(WxPayClient::postXmlToServer($xmlString, $url));
        return $wxResponse;

    }

    /**
     * @brief 查询订单 
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 11:53:48
    **/
    public function query($orderParams) {

        $this->scenario = 'query';
        $this->load($orderParams);
        $this->setSign();
        $wxResponse = new WxPayResponse(WxPayClient::postXmlToServer($xmlString, $url));
        return $wxResponse;

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

    /**
     * @brief 订单退款
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 15:17:59
    **/
    public function refund($orderParams) {

    }
}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
