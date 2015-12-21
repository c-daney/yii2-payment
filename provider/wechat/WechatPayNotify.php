<?php
require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

class WechatPayNotify extends WxPayNotify
{

    public $paySucceededHandler;
    public $payFailedHanlder;
    public $refundSucceededHandler;


    /**
     * @brief 设置通知成功的回调函数
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/18 23:21:12
    **/
    public function setHandlers($handlers) {
        $this->paySucceededHandler = $handlers['paySuccessHandler'];
        $this->payFailedHanlder = $handlers['payFailHanlder'];
    }

	/**
	 * @brief 查询订单状态
	 *
     * @param int $transaction_id 交易号
	 * @return bool 订单是否支付成功 
	 * @see 
	 * @note 
	 * @author 吕宝贵
	 * @date 2015/12/18 10:47:15
	**/
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	/**
	 * @brief 支付结果的回调函数, 业务逻辑可以在此放入成功和失败的处理逻辑回调函数
	 *
	 * @return  public function 
	 * @retval   
	 * @see 
	 * @note 
	 * @author 吕宝贵
	 * @date 2015/12/18 10:49:48
	**/
	public function NotifyProcess($data, &$msg)
    {
        $notfiyOutput = [];

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }

        //查询订单，判断订单真实性,回调订单失败逻辑
        if(!$this->Queryorder($data["transaction_id"])){
            @call_user_func($this->payFailedHanlder, $data);
            $msg = "订单查询失败";
            return false;
        }
        else {
            if (call_user_func($this->paySucceededHandler, $data)) {
                return true;
            }
            else {
                return false;
            }
        }
    }
}

