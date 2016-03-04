<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
/**
 * @file WxPayNotify.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WxPayNotify extends component {

    public function makeSign($params, $key) {
        ksort($params);
        $string = $this->toUrlParams($params);
        $string = $string . '&key=' . $key;
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    } 

}





/* vim: set et ts=4 sw=4 sts=4 tw=100: */
