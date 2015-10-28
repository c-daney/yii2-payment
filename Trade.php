<?php
/**
 * @link http://www.lubanr.com/
 * @copyright Copyright (c) 2015 Baochen Tech. Co. 
 * @license http://www.lubanr.com/license/
 */

namespace lubaogui\payment;

use Yii;
use \yii\db\ActiveRecord;

/**
 * 交易组件接口
 *
 * @author Baogui Lu (lbaogui@lubanr.com)
 * @version since 2.0
 */
class Trade extends ActiveRecord 
{

    public static function tableName() {
        return '{{%trade}}'
    }


    public function attributeLabels() {

        return [
            'id' => '交易id',
            'platform_trade_id' => '交易id,',
            'order_id' => '订单id',
            'order_desc' => '订单描述',
            'money' => '交易金额,以元为单位',
            'unit' => '交易单位',
            'pay_method_id' => '交易方法id',
            'status' => '交易状态',
            'created_at' => '交易创建时间',
            'updated_at' => '交易更新时间',
        ];

    }

    public $tradeFee = 0;
    public $orderId = ;
    public $totalFee;

    /**
     *  支付服务实例
     */
    private $_payment;

    /**
     * provider 支付提供商
     */
    private $_provider;

    /**
     * 构造函数
     * @param provider string 支付供应商名称
     */
    public function __construct($provider) 
    {
        $this->_provider = $provider;
    }

}
