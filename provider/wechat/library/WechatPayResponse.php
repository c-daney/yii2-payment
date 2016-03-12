<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
/**
 * @file WechatPayResponse.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WechatPayResponse extends WechatPayBase {

    public function __construct($xml) {
        if ($xml) {
            $data = $this->transferXmlToArray($xml);
            if (!is_array($data)) {
                return false;
            }
            else {
                $this->setAttributes($data, false);
                if ($this->getAttribute('return_code') !== 'SUCCESS') {
                    $this->addError('display-message:error', $this->getAttribute('return_msg'));
                }
                else {
                    if ($this->getAttribute('result_code') !== 'SUCCESS') {
                        $this->addError('display-message:error', $this->getAttribute('err_code_desc'));
                    }
                }
            }
        }
        else {
            return false;
        }
    }

}





/* vim: set et ts=4 sw=4 sts=4 tw=100: */
