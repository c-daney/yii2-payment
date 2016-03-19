<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment;

use Yii;
use yii\base\Exception;
use lubaogui\account\behaviors\ErrorBehavior;

/**
 * 支付组件接口,暴露给外部的接口,组件虽有models等，但不对外提供功能，对外只提供几个接口函数, 收款和提现
 * 支付宝回告通知可调用本扩展来完成业务逻辑，为了解耦合，订单相关的处理逻辑最好以回调函数方式处理，这样做可以最大化复用
 * 代码
 * @author Baogui Lu (lbaogui@lubanr.com)
 * @version since 2.0
 */
abstract class PayServer extend Object
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
     *  支付服务实例,包含支付实例和支付回告实例，根据实际情况初始化对应实例
     */
    private $_payService;
    private $_notifyService;

    //默认支付方式
    public $defaultPayServer;

    //应收账款记录,对于每个支付的server,需要转换成对应的form后提交
    public $receivable = null; 

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

    /*
     * 获取实际的支付实例,支持chain操作
     * 
     * @return object 支付实例
     */
    abstract public function getPayService();

    /**
     * @brief 获取回告服务实例
     *
     * @return  public functioni 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 19:13:15
    **/
    abstract public function getNotifyService(); 
    
    /*
     * 返回用户客户端提交支付请求的请求参数 
     * 
     * @return array 支付请求的数组信息 
     */
    public function generatePayRequestParams($receivable) {
        return $this->getPayService->generatePayRequestParams($receivable);
    }

    /**
     * @brief 
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2016/03/14 18:47:03
    **/
    public function replySuccessToServer() {
        $this->getNotifyService()->replySuccessToServer();
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
        $this->getNotifyService->replyFailureToServer();
    }

}
