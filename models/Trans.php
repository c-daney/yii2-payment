<?php
namespace lubaogui\payment\models;

use Yii;
use yii\base\NotSupportedException;
use yii\base\Model;

/**
 * Trans model
 */
class Trans extends Model 
{
    public $trans_id = 0;
    public $total_fee = 0;
    public $subject = '';
    public $body = '';
    public $description = '';
    public $out_trade_no = '';

    public function rules() {
        return [
           [['trand_id', 'total_value', 'subject', 'description'], 'required'], 
        ];

    }
}
