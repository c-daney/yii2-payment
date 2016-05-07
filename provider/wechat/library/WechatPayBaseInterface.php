<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
use Yii;
 
/**
 * @file WechatPayBase.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

interface WechatPayBaseInterface   
{

    public function attributes();
    public function getAttribute($name);
    public function setAttribute($name, $value);
    public function hasAttribute($name);

}

/* vim: set et ts=4 sw=4 sts=4 tw=100: */
