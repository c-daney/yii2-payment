<?php
namespace lubaogui\payment\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Receivable model 应收账款
 */
class Receivable extends ActiveRecord
{

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['role', 'default', 'value' => self::ROLE_USER],
        ];
    }

}
