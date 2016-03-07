<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
use yii/base/Model;
 
/**
 * @file WxPayBase.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WxPayBase extends Model 
{

    //所有的wechatPay
    public $appId;
    public $mchId;
    public $key;

    private $_attributes;


    /**
     * @brief 设置签名
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 16:12:02
    **/
    public function setSign() {
        $this->$_attributes['sign'] = $this->makeSign();
    }

    /**
     * @brief 将数据放入到属性列表中
     *
     * @return array  
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 18:20:30
    **/
    public function load($data,  $formName = '') {

    }

    /**
     * @brief 设置属性
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 18:20:17
    **/
    public function setAttributes($data, $safeAttributesOnly = true) {

    }

    /**
     * @brief 获取属性列表
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 11:03:08
    **/
    public function attributes() {
        return $this->_attributes;
    }

    /**
     * @brief 设置属性值 
     *
     * @return  public 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 11:03:21
    **/
    public setAttribute($name) {

    }

    /**
     * @brief 获取属性值
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 11:03:38
    **/
    public function getAttribute($name) {

    }


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
    protected function makeSign($params) {
        ksort($params);
        $string = $this->toUrlFormat($params);
        $string = $string . '&key=' . $this->key;
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
    public function transferXmlToArray($xml) {

        libxml_disable_entity_loader(true);
        $dataArray = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $dataArray;

    }

    /**
     * @brief 将数组转换成xml形式
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/06 13:57:52
    **/
    public function toXml($data) {
	
		if(!is_array($data) 
			|| count($data) <= 0)
		{
    		throw new WxPayException("数组数据异常！");
    	}
    	
    	$xml = "<xml>";
    	foreach ($data as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
    }

}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
