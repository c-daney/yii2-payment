<?php
namespace lubaogui\payment\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use lubaogui\accountt\models\UserAccount;
use lubaogui\accountt\models\Trans;

/**
 * Receivable model 应收账款
 */
class Receivable extends ActiveRecord
{
    //代收账款状态，等待支付，支付成功，已入账
    const PAY_STATUS_WAITPAY = 1;
    const PAY_STATUS_SUCCEEDED = 2;
    const PAY_STATUS_FINISHED = 3;

    /**
     * @brief 
     *
     * @return  public static function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/11/24 00:06:12
    **/
    public static function tableName()
    {
        return '{{%receivable}}';
    }

    /**
     * @brief 
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/11/24 00:06:20
    **/
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @brief 待支付款项支付成功的处理逻辑,此处仅处理用户账户的收款逻辑
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/20 11:59:54
    **/
    public function paySuccess() {

        if (empty($this->userAccount)) {
            return false;
        }
        else {
            $this->status = self::PAY_STATUS_SUCCEEDED; //设置支付成功
            //用户账户增加余额
            if ($this->save() && $this->UserAccount->plus($this->money, '用户充值')) {
                
                //收款关联的交易id问题，交给controller层次去做
                return true;
            }
            return false;
        }

    }


    public function hasPaySucceeded() {
        return $this->status >= self::PAY_STATUS_SUCCEEDED ? true : false;
    }


    public function getUserAccount() {
        return $this->hasOne(UserAccount::className(), ['uid'=>'uid']);
    }


    public function getTrans() {
        return $this->hasOne(Trans::className(), ['id'=>'trans_id']);
    }

}
