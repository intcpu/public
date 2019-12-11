<?php
require_once '../Autoload.php';
use Library\Mail\Email;

$config = [
'EMAIL_HOST' => 'smtp.163.com',
'EMAIL_USERNAME' => 'aaa@163.com',
'EMAIL_PASSWORD' => 'aaa',
'name' => 'test'
];



$url = "http://www.baidu.com";
$content = "<div>";
$content.= "您好，<br><br>请点击链接：<br>";
$content.= "<a target='_blank' href='{$url}' >完成注册邀请</a>";
$content.= "<br><br>如果链接无法点击，请复制并打开以下网址：<br>";
$content.= "<a target='_blank' href='{$url}' >{$url}</a>";
$is_true = Email::send($config['EMAIL_HOST'],$config['EMAIL_USERNAME'],$config['EMAIL_PASSWORD'],$config['name'].'团队','aaa@163.com',$config['name'].'团队[注册邀请]',$content);

var_dump($is_true);