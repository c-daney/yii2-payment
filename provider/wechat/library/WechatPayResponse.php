<?php

/***************************************************************************
 *
 * Copyright (c) 2016 Lubanr.com All Rights Reserved
 *
 **************************************************************************/
 
namespace lubaogui\payment\provider\wechat\library;
 
 
use Yii;
/**
 * @file WechatPayResponse.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2016/03/03 18:00:59
 * @version $Revision$
 * @brief
 *
 **/

class WechatPayResponse extends WechatPayBase {


    public function __construct($xml, $scenario = 'unifiedOrder') {
        if ($xml) {
            $data = $this->transferXmlToArray($xml);
            Yii::warning('Response content of Server is:');
            Yii::warning($data);
            if (!is_array($data)) {
                $this->addError('debug', __METHOD__ . ':服务器返回xml不可解析:' . $xml);
                return false;
            }
            else {
                $this->setScenario($scenario);
                $this->setAttributes($data, false);
                if ($this->getAttribute('return_code') !== 'SUCCESS') {
                    $this->addError(__METHOD__, $this->getAttribute('return_msg'));
                }
                else {
                    if ($this->getAttribute('result_code') !== 'SUCCESS') {
                        $this->addError(__METHOD__, $this->getAttribute('err_code_desc'));
                    }
                }
            }
        }
        else {
            $this->addError(__METHOD__, '服务器返回为空');
            return false;
        }
    }

    public function scenarios() {
        return [
            'unifiedOrder'=>[
                'return_code', 'return_msg', 'appid', 'mch_id', 'device_info', 'nonce_str', 
                'sign', 'result_code', 'err_code', 'err_code_desc', 'trade_type', 'prepay_id',
                'code_url'
            ],
            'query'=>[
                'return_code', 'return_msg', 'appid', 'mch_id', 'nonce_str', 
                'sign', 'result_code', 'err_code', 'err_code_desc', 'device_info', 'openid',
                'is_subscribe', 'trade_type', 'trade_state', 'bank_type', 'total_fee',
                'fee_type', 'cash_fee', 'cash_fee_type', 'coupon_fee', 'coupon_count',
                'out_trade_no', 'attach', 'time_end', 'trade_state_desc' 
            ],
        ];
    }


}





/* vim: set et ts=4 sw=4 sts=4 tw=100: */
