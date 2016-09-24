yii2 payment extension
======================
yii2的支付扩展，支持支付宝，微信支付等,目前正在开发支付宝扩展，目前版本正在开发中，功能尚不完善，请不要在生产环境使用，谢谢.

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
use lubaogui\payment\Receivable;

$payment = new Payment('alipay');

//订单相关信息,详细参数参见支付宝文档
$receivable = new Receivable();

$receivable->money =154;

return $payment->generateUserRequestHtml($receivable);

```

对于支付宝支付，需要开始对配置文件进行配置，配置文件位于Provider\alipay\lib\alipay.config.php ,配置完成后可以使用，扩展主要功能:

1. 产生请求html内容，
2. 验证服务端返回是否合法

对于业务逻辑相关功能，用户可以在自己的产品流程中添加
此模块结合lubaogui/yii2-account模块一起使用，效果更佳。
in yii2-account module, the controller file AccountController shows the usage of this module.


如果有愿意一起讨论开发的，请加 QQ: 6649216 谢谢。
