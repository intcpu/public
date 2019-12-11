<?php
$a = '2.000000';
$a = rtrim(bcadd($a, 0, 8), '0');

$_SERVER['HTTP_REFERER'] = 'https://u.bibidev.com';
preg_match('/^(http|https)\:\/\/([a-zA-Z\-]+\.){2,2}(com|pro)/', $_SERVER['HTTP_REFERER'],$matches);
var_dump($matches);
exit();

$a = '%s 分钟内最多发送 %s 封邮件，请稍后再试';
//$b = 1;
$b = [5,5];
array_unshift($b,$a);
$t = call_user_func_array('sprintf',$b);
var_dump($t);