<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment\provider\alipay;

use lubaogui\payment\provider\BasePayServer;
use lubaogui\payment\models\Receivable;

/**
 * 支付宝服务类，主要用于产生支付宝请求和校验支付宝的服务器返回.
 *
 *
 * Additionally, when attaching an event handler, extra data may be passed
 * and be available via the [[data]] property when the event handler is invoked.
 *
 * @author Lu Baogui <lbaogui@lubanr.com>
 * @since 2.0
 */
class PayServer extends BasePayServer
{
    /*
     * alipay 接口类实例
     */
    private $alipay = null;

    /**
     * 构造函数 
     *
     * @param array $alipayConfig 配置信息，配置信息重require文件中获得 
     */
    public function __construct() 
    {
       $config = require(dirname(__FILE__) . '/config/alipay.config.php'); 
       $this->alipay = new Alipay($config);
    }

    /**
     * 产生返回给用户浏览器向支付宝服务器提交支付支付请求的html代码,此处完成Receivable向order的转换
     *
     * @param Receivable object 请求数组
     */
    public function generateUserRequestHtml($receivable) 
    {
        $submitToAlipayParams = $this->transformToAlipayParams($receivable);
        $requestHtml = $this->alipay->buildRequestForm($submitToAlipayParams, 'post', 'confirm');
        return $requestHtml;
    }

    /**
     * 验证支付宝的服务器返回
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function processNotify($handlers) 
    {
        if ($this->alipay->verifyNotify())
        {
            $notifyData = $this->alipay->getNotifyData();
            if ($notifyData['trade_status'] == 'TRADE_SUCCESS') {
                $receivableId = $notifyData['out_trade_no'];
                $receivable = Receivable::fineOne($receivableId);
                if (empty($receivable) || $receivable->status == Receivable::PAY_STATUS_FINISHED) {
                    return false;
                }
                return call_user_func($handlers['paySuccessHandler'], $receivable);
            }
            else {
                return false;
            }
        }
        else {
            call_user_func($handlers['payFailHandler'], []);
            return false;
        }
    }

    /**
     * 获取支付的支付状态 
     *
     * @return boolen 返回验证状态, true代表合法请求，fasle代表无效返回
     */
    public function getPayStatus() 
    {
        $returnStatus = $_POST['trade_status'];
        $payStatus = Payment::PAY_STATUS_CREATE;
        switch ($tradeStatus) {
            case 'WAIT_BUYER_PAY': {
                $payStatus = Payment::PAY_STATUS_CREATED;               
                break;
            }
            case 'TRADE_FINISHED': {
                $payStatus = Payment::PAY_STATUS_FINISHED;               
                break;
            }
            case 'TRADE_SUCCESS': {
                $payStatus = Payment::PAY_STATUS_SUCCEEDED;               
                break;
            }
            case 'TRADE_CLOSED': {
                $payStatus = Payment::PAY_STATUS_CLOSED;               
                break;
            }
            default: break;
        }
    }

    /**
     * @brief 生成用于支付的扫描二维码
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/25 23:56:10
    **/
    public function generateUserScanQRCode($receivable) { 
        return ''; 
    }

    /**
     * @brief 处理用户返回
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/25 23:55:54
    **/
    public function processReturn() { 
    } 

    /**
     * @brief 将Receivable转换成符合支付宝的支付参数
     *
     * @return  protected function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/26 12:04:41
    **/
    protected function transformToAlipayParams($receivable) {

        $alipayParams = [];
        
        $alipayParams['out_trade_no'] = $receivable->id;
        $alipayParams['subject'] = $receivable->description;
        $alipayParams['total_fee'] = $receivable->money;
        $alipayParams['body'] = $receivable->description;
        $alipayParams['show_url'] = '';
        $alipayParams['notify_url'] = 'http://www.mr-hug.com/account/alipay-notify';
        $alipayParams['return_url'] = 'http://www.mr-hug.com/user-booking';

        return $alipayParams;

    }

}
