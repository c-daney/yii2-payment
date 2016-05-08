<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use lubaogui\account\behaviors\ErrorBehavior;;

/**
 * 支付组件接口,暴露给外部的接口,组件虽有models等，但不对外提供功能，对外只提供几个接口函数, 收款和提现
 * 支付宝回告通知可调用本扩展来完成业务逻辑，为了解耦合，订单相关的处理逻辑最好以回调函数方式处理，这样做可以最大化复用
 * 代码
 * @author Baogui Lu (lbaogui@lubanr.com)
 * @version since 2.0
 */

class Payment extends Model
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
    private $_payServer;

    /**
     * provider 支付提供商名称
     */
    private $_provider;

    //应收账款记录,对于每个支付的server,需要转换成对应的form后提交
    public $receivable = null; 

    /**
     * 支付方法对应的支付服务类
     */
    private $_payServerMap = [
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
    public function __construct($provider = 'alipay', $options = []) 
    {
        $this->_provider = $provider;

        if (isset($this->_payServerMap[$this->_provider])) {
            if (is_array($this->_payServerMap[$this->_provider])) {
                $baseConfig = $this->_payServerMap[$this->_provider];
            }
            else {
                if (is_string($this->_payServerMap[$this->_provider])) {
                    $baseConfig = ['class' => $this->_payServerMap[$this->_provider]];
                }
                else {
                    throw new Exception('error occured when init payserver');
                }
            }
            $config = array_merge($baseConfig, $options);
            $this->_payServer = Yii::createObject($config);
            if (!$this->_payServer) {
                throw new Exception('error occured when init payserver');
            }
        }
        else {
            throw new Exception('payment server your specified ' . $this->_provider . ' is not supported now!');
        }
    }

    /*
     * 获取实际的支付实例,支持chain操作
     * 
     * @return object 支付实例
     */
    public function getPayServer() 
    {
        return $this->_payServer;
    }

    /*
     * 获取支付供应商支付名称 
     * 
     * @return string 支付供应商名称
     */
    public function getProvider() 
    {
        return $this->_provider;
    }

    /*
     * 返回用户客户端提交支付请求的请求参数或者支付页面 
     * 
     * @return array 支付请求的数组信息 
     */
    public function generatePayRequestParams($receivable) {

        return $this->getPayServer()->generatePayRequestParams($receivable);

    }

    /**
     * @brief 检查某个订单信息的支付结果信息,验证会包含远程调用，不要放在事物中处理
     *
     * @return bool  
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 19:20:36
    **/
    public function checkPayStatus($orderId = null) {
        return $this->getPayServer()->checkPayStatus($orderId);
    }

    /**
     * @brief 获取支付通知对应的本地订单
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/05/08 22:11:54
    **/
    public function getReceivable($receivableId = null) {
        return $this->getPayServer()->getReceivable($receivable);
    }

    /**
     * @brief 
     *
     * @return  bool 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 19:24:19
    **/
    public function checkRefundStatus($orderId = null) {
        return $this->getPayServer()->checkRefundStatus($orderId);
    }

    /**
     * @brief 
     *
     * @return  
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 18:47:03
    **/
    public function replySuccessToServer() {
        $this->getPayServer()->replySuccessToServer();
    }

    /**
     * @brief 
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 18:47:07
    **/
    public function replyFailureToServer() {
        $this->getPayServer()->replyFailureToServer();
    }

    /**
     * @brief 处理回告通知
     *
     * @param array $handlers 支付回告的回调处理函数
     * @return int $trans_id 返回交易id
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/28 07:29:47
    **/
    public function processNotify($handlers) {
        return $this->getPayServer()->processNotify($handlers);
    }

}
