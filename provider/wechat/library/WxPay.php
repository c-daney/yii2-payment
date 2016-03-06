<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
use yii/base/Model;
 
/**
 * @file WxPay.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WxPay extends Model 
{

    /**
     * @brief 生成签名
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/04 14:40:30
    **/
    public function makeSign($params, $key) {
        ksort($params);
        $string = $this->toUrlFormat($params);
        $string = $string . '&key=' . $key;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    } 


    /**
     * @brief 将数组转化为url形式
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/04 14:40:30
    **/
    public function toUrlFormat($params) {
        $buff = '';
        foreach ($params as $k => $v) {
            if ($key != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . '&';
            }
            $buff = trim($buff, '&');
            return $buff;
        }
    }


    /**
     * @brief 从xml内容中解析出数组结果
     *
     * @return array 从xml中解析出来的数组 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 11:12:11
    **/
    public function fromXml($xml) {

        libxml_disable_entity_loader(true);
        $dataArray = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $dataArray;

    }

}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
