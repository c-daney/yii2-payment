<?php
/***************************************************************************
 *
 * Copyright (c) 2015 Lubanr.com All Rights Reserved
 *
 **************************************************************************/

namespace lubaogui\payment\provider\alipay;

/**
 * @file Alipay.php
 * @author 吕宝贵(lbaogui@lubanr.com)
 * @date 2015/11/10 16:04:11
 * @version $Revision$
 * @brief
 *
 **/


class Alipay {

    // 配置信息在实例化时从配置文件读入，配置文件需要放在该文件同目录下
    private $config = [];
    private $notifyData = [];

    private $service               = 'create_direct_pay_by_user';
    private $serviceMobile           = 'alipay.wap.trade.create.direct';

    private $alipayGateway        = 'https://mapi.alipay.com/gateway.do?';
    private $alipayGatewayMobile = 'http://wappaygw.alipay.com/service/rest.htm?';
    private $verifyUrl            = 'http://notify.alipay.com/trade/notify_query.do?';
    private $verifyUrlHttps      = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

    function __construct($config, $isMobile = false){
        $this->config = $config;
        $this->isMobile = $isMobile;
        if ($isMobile) {
            $this->service = $this->serviceWap;
            $this->alipayGateway = $this->alipayGatewayMobile;
        }
    }

    /**
     * 根据请求参数，生成请求参数的签名
     * 
     * @param $params <Array> 该数组是已经经过ksort之后的请求参数数组，而不是原始请求参数数组
     * @return <String> 签名结果
     * 未考虑参数中空格被编码成加号“+”等情况
     */
    function buildRequestSign($params) {
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
    public function buildRequestParams($params) {
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
     * @brief 生成客户端支付需要的string
     *
     * @return string 支付字符串 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/02/26 15:50:41
    **/
    public function buildRequestString($params) {
        $processParams = $this->buildRequestParams($params);
        $submitString = http_build_query($processParams);
        return $submitString;
    }

    /**
     * 生成发送表单HTML
     * 其实这个函数没有必要，更应该使用签名后的参数自己组装，只不过有时候方便就从官方 SDK 里留下了。
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

    /**
     * 准备移动网页支付的请求参数
     * 
     * 移动网页支付接口不同，需要先服务器提交一次请求，拿到返回 token 再返回客户端发起真实支付请求。
     * 该方法只完成第一次服务端请求，生成参数后需要客户端另行处理（可调用`buildRequestFormHTML`生成表单提交）。
     * 
     * @param $params <Array>
     *        $params['out_trade_no'] 订单唯一编号
     *        $params['subject']      商品标题
     *        $params['total_fee']    支付总费用
     *        $params['merchant_url'] 商品链接地址
     *        $params['req_id']       请求唯一 ID
     * 
     * @return <Array>
     */
    function prepareMobileTradeData($params) {
        // 不要用 SimpleXML 来构建 xml 结构，因为有第一行文档申明支付宝验证不通过
        $xml_str = '<direct_trade_create_req>' .
            '<notify_url>' . $this->config['notify_url'] . '</notify_url>'.
            '<call_back_url>' . $this->config['return_url'] . '</call_back_url>'.
            '<seller_account_name>' . $this->config['seller_email'] . '</seller_account_name>'.
            '<out_trade_no>' . $params['out_trade_no'] . '</out_trade_no>'.
            '<subject>' . htmlspecialchars($params['subject'] , ENT_XML1, 'UTF-8') . '</subject>'.
            '<total_fee>' . $params['total_fee'] . '</total_fee>'.
            '<merchant_url>' . $params['merchant_url'] . '</merchant_url>' .
            '</direct_trade_create_req>';
        $request_data = $this->buildSignedParameters(array(
            'service'           => $this->service,
            'partner'           => $this->config['partner'],
            'sec_id'            => $this->config['sign_type'],
            'format'            => 'xml',
            'v'                 => '2.0',
            'req_id'            => $params['req_id'],
            'req_data'          => $xml_str,

            '_input_charset'    => $this->config['input_charset']
        ));
        $url = $this->alipayGateway;
        $input_charset = trim(strtolower($this->config['input_charset']));
        if (trim($input_charset) != '') {
            $url = $url."_input_charset=".$input_charset;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_CAINFO, $this->config['cacert']);//证书地址
        curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl, CURLOPT_POST, true); // post传输数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);// post传输数据
        $responseText = curl_exec($curl);
        //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        parse_str($responseText, $responseData);
        if( ! empty ($responseData['res_data'])) {
            if($this->config['sign_type'] == '0001') {
                $responseData['res_data'] = rsaDecrypt($responseData['res_data'], $this->config['private_key_path']);
            }
            //token从res_data中解析出来（也就是说res_data中已经包含token的内容）
            $doc = new DOMDocument();
            $doc->loadXML($responseData['res_data']);
            $responseData['request_token'] = $doc->getElementsByTagName( "request_token" )->item(0)->nodeValue;
        }
        $xml_str = '<auth_and_execute_req>'.
            '<request_token>' . $responseData['request_token'] . '</request_token>'.
            '</auth_and_execute_req>';
        return array(
            'service'           => 'alipay.wap.auth.authAndExecute',
            'partner'           => $this->config['partner'],
            'sec_id'            => $this->config['sign_type'],
            'format'            => 'xml',
            'v'                 => '2.0',
            'req_data'          => $xml_str
        );
    }

    /**
     * @brief 返回支付宝返回的数据
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/01/16 00:25:41
    **/
    public function getNotifyData() {
        return $this->notifyData;
    }

    /**
     * 支付完成验证返回参数（包含同步和异步）
     * 
     * @param $async <Boolean> 是否异步通知模式
     * 
     * @return <Boolean>
     */
    function verifyNotify() {
        $async = empty($_GET);
        $data = $async ? $_POST : $_GET;
        if (empty($data)) {
            return false;
        }
        $this->notifyData = $data;
        $signValid = $this->verifyParameters($data, $data["sign"]);
        $notify_id = $data['notify_id'];
        if ($async && $this->is_mobile){
            //对notify_data解密
            if ($this->config['sign_type'] == '0001') {
                $data['notify_data'] = $this->rsaDecrypt($data['notify_data'], $this->config['private_key_path']);
            }

            //notify_id从decrypt_post_para中解析出来（也就是说decrypt_post_para中已经包含notify_id的内容）
            $doc = new DOMDocument();
            $doc->loadXML($data['notify_data']);
            $notify_id = $doc->getElementsByTagName( 'notify_id' )->item(0)->nodeValue;
        }
        //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
        $responseTxt = 'true';
        if (! empty($notify_id)) {
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
     * 
     * @return <Boolean>
     */
    function verifyParameters($params, $sign) {
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

    function filterParams($params) {
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

    function verifyFromServer($notify_id) {
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
     * RSA验签
     * @param $data 待签名数据
     * @param $ali_public_key_path 支付宝的公钥文件路径
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    function rsaVerify($data, $ali_public_key_path, $sign)  {
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
    protected function rsaDecrypt($content, $private_key_path) {
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

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
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

}
/* vim: set et ts=4 sw=4 sts=4 tw=100: */
