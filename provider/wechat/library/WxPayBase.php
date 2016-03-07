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

    private $_attributes = [];

    /**
     * @brief 获取属性名称
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 15:49:24
    **/
    public function __get($name) {

        if (isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        } elseif ($this->hasAttribute($name)) {
            return null;
        }
        else {
            if (isset($this->_related[$name]) || array_key_exists($name, $this->_related)) {
                return $this->_related[$name];
            }
            $value = parent::__get($name);
            return $value;
        }

    }

    /**
     * @brief 设置某个属性值 PHP magic function
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 15:49:36
    **/
    public function __set($name, $value) {

        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        }
        else {
            parent::__set($name, $value);
        }

    }

    /**
     * @brief 判断是否含有某个名称的属性
     *
     * @return  bool 是否包含该属性
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 16:14:00
    **/
    public function hasAttribute($name) {
        return isset($this->_attributes[$name]) || in_array($name, $this->attributes());
    }

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
    public function load($data,  $formName = null) {
        //load的时候需要考虑数据安全性，是否所有属性可以批量导入
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) { 
            $this->setAttributes($data);
            return true;
        }
        elseif (isset($data[$scope])) {
            $this->setAttributes($data[$scope]);
            return true;
        }
        else {
            return false;
        }
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
    public function setAttributes($data, $safeOnly = true) {
        if (is_array($data)) {
            $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
            foreach ($data as $name => $value) {
                if (isset($attributes[$name])) {
                    $this->$name = $value;
                } elseif ($safeOnly) {
                    $this->onUnsafeAttribute($name, $value);
                }
            }
        }
    }

    public function safeAttributes() {
        $scenario = $this->getScenario();
        $scenarios = $this->scenarios();
        if (!isset($scenarios[$scenario])) {
            return [];
        }
        $attributes = [];
        foreach ($scenarios[$scenario] as $attribute) {
            if ($attribute[0] !== '!') {
                $attributes[] = $attribute;
            }
        }
        return $attributes;
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

    public function getScenario() {
        return $this->_scenario;
    }

    public function setScenario($value) {
        $this->_scenario = $value;
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
