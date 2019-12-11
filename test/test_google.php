<?php
require_once '../Autoload.php';
use Library\GoogleAuth;
use Library\GoogleAuth2;

$ga = new GoogleAuth();
$secret = $ga->createSecret();
$secret = 'aaaaaaaaaa';//用户唯一一个密钥，上面生成的

//下面为生成二维码，内容是一个URI地址（otpauth://totp/账号?secret=密钥&issuer=标题）
//例子：otpauth://totp/test@163.com?secret=aaaaaaaaaa&issuer=test
$qrCodeUrl = $ga->getQRCodeGoogleUrl('test@163.com', $secret, 'shisha');
var_dump($qrCodeUrl);

//下面为验证参数
$oneCode = '';

//下面为验证用户输入的code是否正确
$checkResult = $ga->verifyCode($secret, $oneCode, 2);    // 2 = 2*30秒 时钟容差

$code = $ga->getCode($secret);
//$code = '951502';
var_dump($code);

var_dump($ga->verifyCode($secret,$code,3));

exit();
