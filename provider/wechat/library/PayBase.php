<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
use yii\base\Model;
use yii\base\InvalidParamException;
use lubaogui\payment\provider\wechat\library;

 
/**
 * @file PayBase.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

abstract class PayBase extends Model implements WechatPayBaseInterface
{

    private $_attributes = [];
    private $_related = [];

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
     * @brief 设置某个属性
     *
     * @return  bool 是否包含该属性
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/07 16:14:00
    **/
    public function setAttribute($name, $value) {

        if ($this->hasAttribute($name)) {
            $this->_attributes[$name] = $value;
        }
        else {
            throw new InvalidParamException(get_class($this) . 'has no attribute named' . $name);
        }

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


}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
