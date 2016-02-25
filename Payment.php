<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment;

use Yii;
use yii\base\Exception;
use lubaogui\account\behaviors\ErrorBehavior;;

/**
 * 支付组件接口,暴露给外部的接口,组件虽有models等，但不对外提供功能，对外只提供几个接口函数, 收款和提现
 * 支付宝回告通知可调用本扩展来完成业务逻辑，为了解耦合，订单相关的处理逻辑最好以回调函数方式处理，这样做可以最大化复用
 * 代码
 * @author Baogui Lu (lbaogui@lubanr.com)
 * @version since 2.0
 */
class Payment 
{

    /**
     *  消息定义
     */
    const EVENT_PAY_SUCCEEDED = 'paySucceeded';
    const EVENT_PAY_FAILED = 'payFailed';
    const EVENT_REFUND_SUCCEEDED = 'refundSucceeded';
    const EVENT_REFUND_FAILED = 'refundFailed';

    /**
     * 支付状态定义
     */
    const PAY_STATUS_CREATED = 0;
    const PAY_STATUS_FINISHED = 10;
    const PAY_STATUS_CLOSED =   20;
    const PAY_STATUS_SUCCEEDED = 30;
    const PAY_STATUS_REFUNDED = 40;

    /**
     *  支付服务实例
     */
    private $payServer;

    /**
     * provider 支付提供商名称
     */
    private $provider;

    //应收账款记录,对于每个支付的server,需要转换成对应的form后提交
    public $receivable = null; 

    /**
     * 支付方法对应的支付服务类
     */
    private $payServerMap = [
        'alipay' => 'lubaogui\payment\provider\alipay\PayServer',
        'wechatpay' => 'lubaogui\payment\provider\wechat\PayServer',
    ];

    /**
     * @brief 默认的错误behaviors列表，此处主要是追加错误处理behavior
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/30 16:55:03
    **/
    public function behaviors() {
        return [
            ErrorBehavior::className(),
        ];
    }

    /**
     * 构造函数
     * @param provider string 支付供应商名称
     */
    public function __construct($provider = 'alipay') 
    {
        $this->provider = $provider;
        if (isset($this->payServerMap[$this->provider])) {
            $config = !is_array($this->payServerMap[$this->provider]) ? 
                ['class' => $this->payServerMap[$this->provider]] : 
                $this->payServerMap[$this->provider];
            $this->payServer = Yii::createObject($config);
            return $this->payServer;
        }
        else {
            throw new Exception('payment server your specified ' . $this->provider . ' is not supported now!');
        }
    }

    /*
     * 获取实际的支付实例,支持chain操作
     * 
     * @return object 支付实例
     */
    public function getPayServer() 
    {
        return $this->payServer;
    }

    /*
     * 获取支付供应商支付名称 
     * 
     * @return string 支付供应商名称
     */
    public function getProvider() 
    {
        return $this->provider;
    }

    /**
     * @brief 去支付方法，去支付里面可以添加相关逻辑，判断支付的跳转形式
     *
     * @return string 跳转的js代码或者是扫描二维码的imageStr 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/19 19:23:53
    **/
    public function gotoPay($receivable, $returnType = 'QRCodeUrl') {

        if ($returnType === 'QRCodeUrl') {
            return $this->generateUserScanQRCode($receivable);
        }
        else {
            return $this->generateUserRequestHtml($receivable);
        }

    }


    /*
     * 跳转到第三方支付平台支付页面
     * 
     * @return string 支付block内容页面,通常是自动的js跳转
     */
    public function generateUserRequestHtml($receivable) {

        return $this->payServer->generateUserRequestHtml($receivable);
    }

    /*
     * 返回用户客户端提交支付请求的请求参数 
     * 
     * @return array 支付请求的数组信息 
     */
    public function generateUserRequestParams($receivable) {

        return $this->payServer->generateUserRequestParams($receivable);

    }

    /*
     * 产生用于用户扫码支付的二维码
     * 
     * @return string 支付block内容页面,通常是自动的js跳转
     */
    public function generateUserScanQRCode($receivable) {
        if (empty($receivable)) {
            throw new Exception('trans info must be set!');
        }

        return $this->payServer->generateUserScanQRCode($receivable);
    }

    /**
     * @brief 处理支付回告逻辑
     *
     * @return  public function 
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/21 20:21:38
    **/
    public function processNotify($handlers) {

        return $this->payServer->processNotify($handlers);

    }

}
