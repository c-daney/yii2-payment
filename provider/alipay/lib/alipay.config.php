<?php

return [
        //合作身份者id，以2088开头的16位纯数字
        'partner' => '',
        //商家注册邮件地址
        'seller_email' => '',
        //当采用md5签名方式时，需要提供key
        'key' => '',
        //商户的私钥（后缀是.pen）文件相对路径
        'private_key_path' => '',
        //支付宝公钥（后缀是.pen）文件相对路径
        'ali_public_key_path' => '',
        //签名方式 不需修改,支持md5和rsa, md5验证，请填写上面的key配置，rsa请配置私匙和公匙 md5: MD5, rsa:RSA
        'sign_type' => 'MD5',
        //字符编码，统一为utf-8
        'input_charset' => 'utf-8',
        //ca证书路径地址，用于curl中ssl校验
        ////请保证cacert.pem文件在当前文件夹目录中
        'cacert' => dirname(__FILE__).'/cacert.pem',
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        'transport' => 'http',
    ];
