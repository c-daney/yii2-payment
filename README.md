yii2 payment extension
======================
yii2的支付扩展，支持支付宝，微信支付等,目前处于开发阶段

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist lubaogui/yii2-payment "*"
```

or add

```
"lubaogui/yii2-payment": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
use lubaogui\payment\Payment;

$payment = new Payment('alipay');

//订单相关信息,详细参数参见支付宝文档
$params = [
    'title' => '',
    'xxx' => 'xxx',
];

echo $payment->generateRequest($params);
exit;

```
