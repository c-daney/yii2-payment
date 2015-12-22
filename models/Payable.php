<?php
namespace lubaogui\payment\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Payable model
 */
class Payable extends ActiveRecord
{
    const PAY_STATUS_WAITPAY = 1;
    const PAY_STATUS_PAYING = 2;
    const PAY_STATUS_FINISHED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payable}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['role', 'default', 'value' => self::ROLE_USER],
        ];
    }

    /**
     * @brief 获取该支付记录对应的账户
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/22 10:50:25
    **/
    public function getUserAccount() {
        return $this->hasOne(UserAccount::className, ['uid'=>'uid']);
    }

    /**
     * @brief 完成支付
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/22 14:24:12
    **/
    public function finishPay() {
        if ($this->trans->status === Trans::PAY_STATUS_FINISHED) {
            return false;
        }
        else {
            $this->status = self::PAY_STATUS_FINISHED;
            $this->finish_time = time();
            if ($this->save()) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    /**
     * @brief 获取对应的trans记录
     *
     * @return  public function 
     * @retval   
     * @see 
     * @note 
     * @author 吕宝贵
     * @date 2015/12/22 10:55:02
    **/
    public function getTrans() {
        return $this->hasOne(Trans::className(), ['id'=>'trans_id']);
    }

}
