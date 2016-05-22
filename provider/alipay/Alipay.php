<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Lubanr.com All Rights Reserved
 *
 **************************************************************************/

namespace lubaogui\payment\provider\alipay;

use  lubaogui\payment\provider\alipay\library\AlipayBase;
/**
 * @file Alipay.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2015/11/10 16:04:11
 * @version $Revision$
 * @brief
 *
 **/


class Alipay extends AlipayBase {

    // 配置信息在实例化时从配置文件读入，配置文件需要放在该文件同目录下
    private $config = [];

    public function __construct($config, $isMobile = false){
        $this->config = $config;
        $this->isMobile = $isMobile;
        if ($isMobile) {
            $this->service = $this->serviceWap;
            $this->alipayGateway = $this->alipayGatewayMobile;
        }
    }

    /**
     * @brief 产生用于支付的参数列表(供客户端或者网页使用)
     *
     * @return array 支付参数列表   
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/02/26 00:12:06
    **/
    public function generatePayRequestParams($receivable) {

        $alipayParams = [];
        
        $alipayParams['out_trade_no'] = $receivable->id;
        $alipayParams['subject'] = $receivable->description;
        $alipayParams['total_fee'] = round($receivable->money, 2);
        $alipayParams['body'] = $receivable->description;
        $alipayParams['show_url'] = '';
        $alipayParams['notify_url'] = $this->config['notify_url'];
        $alipayParams['return_url'] = $this->config['return_url'];

        return $this->buildRequestForm($alipayParams);

    }

    /**
     * @brief 生成发送表单HTML
     * 
     * @param $params <Array> 请求参数（未签名的）
     * @param $method <String> 请求方法，默认：post，可选 get
     * @param $target <String> 提交目标，默认：_self
     *
     * @return string 返回给用户的跳转到支付宝的html字符串
     */
    public function buildRequestForm($params, $method = 'post', $target = '_self') {
        $params = $this->buildRequestParams($params);
        $html = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->alipayGateway."_input_charset=".trim(strtolower($this->config['input_charset']))."' method='$method' target='$target'>";
        foreach ($params as $key => $value) {
            $html .= "<input type='hidden' name='$key' value='$value'/>";
        }
        $html .= "</form><script>document.forms['alipaysubmit'].submit();</script>";
        return $html;
    }

}
/* vim: set et ts=4 sw=4 sts=4 tw=100: */
