<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Lubanr.com All Rights Reserved
 *
 **************************************************************************/

namespace lubaogui\payment\provider\alipay\library;

/**
 * @file Alipay.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2015/11/10 16:04:11
 * @version $Revision$
 * @brief
 *
 **/

class AlipayBase {

    // 配置信息在实例化时从配置文件读入，配置文件需要放在该文件同目录下
    protected $config = [];

    private $service               = 'create_direct_pay_by_user';
    private $serviceMobile           = 'alipay.wap.trade.create.direct';

    protected $alipayGateway        = 'https://mapi.alipay.com/gateway.do?';
    protected $alipayGatewayMobile = 'http://wappaygw.alipay.com/service/rest.htm?';

    private $verifyUrl            = 'http://notify.alipay.com/trade/notify_query.do?';
    private $verifyUrlHttps      = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

    private $_notifyData;



    /**
     * 生成签名后的请求参数
     * 
     * @param $params <Array>
     *        $params['out_trade_no']     唯一订单编号
     *        $params['subject']
     *        $params['total_fee']
     *        $params['body']
     *        $params['show_url']
     *        $params['anti_phishing_key']
     *        $params['exter_invoke_ip']
     *        $params['it_b_pay']
     *        $params['_input_charset']
     *
     * @return <Array>
     * 
     */
     protected function buildRequestParams($params) {
        $baseParams = [ 
            'service' => $this->service,
            'partner' => $this->config['partner']
        ];
        if (!$this->is_mobile) {
            $baseParams = array_merge($baseParams, [ 
                'seller_email'=> trim($this->config['seller_email']),
                'payment_type' => $this->config['payment_type'],
                '_input_charset'=> trim(strtolower($this->config['input_charset'])),
                ]
            );
        }
        //签名之前，需要对参数进行过滤，签名和签名相关的字段需要被过滤掉
        $params = $this->filterParams(array_merge($baseParams, $params));
        ksort($params);
        reset($params);
        $params['sign'] = $this->buildRequestSign($params);
        if ($params['service'] != 'alipay.wap.trade.create.direct' && $params['service'] != 'alipay.wap.auth.authAndExecute') {
            $params['sign_type'] = strtoupper(trim($this->config['sign_type']));
        }
        return $params;
    }

    /**
     * 根据请求参数，生成请求参数的签名,产生支付请求和校验时使用
     * 
     * @param $params <Array> 该数组是已经经过ksort之后的请求参数数组，而不是原始请求参数数组
     * @return <String> 签名结果
     * 未考虑参数中空格被编码成加号“+”等情况
     */
    private function buildRequestSign($params) {
        $paramStr = $this->generateQueryString($params);
        $result = "";
        switch (strtoupper(trim($this->config['sign_type']))) {
        case "MD5" :
            $signStr = $paramStr . $this->config['key'];
            $result = md5($signStr);
            break;
        case "RSA" :
        case "0001" :
            $priKey = file_get_contents($this->config['private_key_path']);
            $res = openssl_get_privatekey($priKey);
            openssl_sign($paramStr, $sign, $res);
            openssl_free_key($res);
            //base64编码
            $result = base64_encode($sign);
            break;
        default :
            $result = "";
        }
        return $result;
    }


    /**
     * @brief 验证支付宝回告的真实性（包含同步和异步）
     * @param $async <Boolean> 是否异步通知模式
     * @return <Boolean>
     */
    protected function verifyNotify() {
        $async = empty($_GET);
        $data = $async ? $_POST : $_GET;
        if (empty($data)) {
            return false;
        }
        $this->_notifyData = $data;
        $signValid = $this->verifyParameters($data, $data["sign"]);
        $notifyId = $data['notify_id'];
        if ($async && $this->is_mobile){
            //对notify_data解密
            if ($this->config['sign_type'] == '0001') {
                $data['notify_data'] = $this->rsaDecrypt($data['notify_data'], $this->config['private_key_path']);
            }

            //notify_id从decrypt_post_para中解析出来（也就是说decrypt_post_para中已经包含notify_id的内容）
            $doc = new DOMDocument();
            $doc->loadXML($data['notify_data']);
            $notifyId = $doc->getElementsByTagName( 'notify_id' )->item(0)->nodeValue;
        }
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'true';
        if (! empty($notifyId)) {
            $responseTxt = $this->verifyFromServer($notify_id);
        }
        //验证
        //$signValid的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
        //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
        return $signValid && preg_match("/true$/i", $responseTxt);
    }

    /**
     * 支付完成验证返回参数（包含同步和异步）
     * 
     * @param $async <Boolean> 是否异步通知模式
     * @return <Boolean>
     */
    protected function verifyParameters($params, $sign) {
        $params = $this->filterParams($params);
        if (!$this->is_mobile) {
            ksort($params);
            reset($params);
        } else {
            $params = array(
                'service' => $params['service'],
                'v' => $params['v'],
                'sec_id' => $params['sec_id'],
                'notify_data' => $params['notify_data']
            );
        }
        $content = urldecode(http_build_query($params));
        switch (strtoupper(trim($this->config['sign_type']))) {
        case "MD5" :
            return md5($content . $this->config['key']) == $sign;
        case "RSA" :
        case "0001" :
            return $this->rsaVerify($content, $this->config['private_key_path'], $sign);
        default :
            return FALSE;
        }
    }

    /**
     * @brief 验证服务器返回
     *
     * @return  protected function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/05/21 22:02:09
    **/
    protected function verifyFromServer($notifyId) {
        $transport = strtolower(trim($this->config['transport']));
        $partner = trim($this->config['partner']);
        $veryfyUrl = ($transport == 'https' ? $this->verifyUrlHttps : $this->verifyUrl) . "partner=$partner&notify_id=$notify_id";
        $curl = curl_init($veryfyUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_CAINFO, $this->config['cacert']);//证书地址
        $responseText = curl_exec($curl);
        // var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        return $responseText;
    }

    /**
     * @brief 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param <Array> $params 需要拼接的数组
     * @param <Bool> $urlencode 是否需要urlencode,默认不需要
     * return 拼接完成以后的字符串
     */
    protected function generateQueryString($params, $urlencode = false) {
        $arg  = "";
        foreach ($params as $key => $val) {
            if ($urlencode == true) {
                $arg .= $key . "=" . urlencode($val) . '&';
            }
            else {
                $arg .= $key . "=" . $val . "&";
            }
        }
        //去掉最后一个&字符
        $arg = substr($arg,0,count($arg)-2);

        //如果存在转义字符，那么去掉转义
        if(get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }

    /**
     * @brief 参数过滤，sign, sign_type和值为空的键不参加签名
     *
     * @return  protected function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/05/21 22:01:00
    **/
    private function filterParams($params) {
        $result = [];
        foreach ($params as $key => $value) {
            if ($key == "sign" || $key == "sign_type" || $value == '') {
                continue;
            }
            else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * RSA验签
     * @param $data 待签名数据
     * @param $ali_public_key_path 支付宝的公钥文件路径
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    private function rsaVerify($data, $ali_public_key_path, $sign)  {
        $pubKey = file_get_contents($ali_public_key_path);
        $res = openssl_get_publickey($pubKey);
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);    
        return $result;
    }

    /**
     * RSA解密
     * @param $content 需要解密的内容，密文
     * @param $private_key_path 商户私钥文件路径
     * return 解密后内容，明文
     */
    private function rsaDecrypt($content, $private_key_path) {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        //用base64将内容还原成二进制
        $content = base64_decode($content);
        //把需要解密的内容，按128位拆开解密
        $result  = '';
        for($i = 0; $i < strlen($content) / 128; $i++  ) {
            $data = substr($content, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        return $result;
    }

}
/* vim: set et ts=4 sw=4 sts=4 tw=100: */
