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
    public $total_value = 0;
    public $subject = '';
    public $description = '';

    public function rules() {
        return [
           [['trand_id', 'total_value', 'subject', 'description'], 'required'], 
        ];

    }
}
