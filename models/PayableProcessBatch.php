<?php
namespace lubaogui\payment\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use lubaogui\account\models\Trans;

/**
 * Payable model
 */
class PayableProcessBatch extends ActiveRecord
{
    const BATCH_PAY_STATUS_PAYING = 0;
    const BATCH_PAY_STATUS_FINISHED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payable_process_batch}}';
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
        ];
    }

}
