<?php
$mobile = '';
$email  = '456';

var_dump(bin2hex(random_bytes(19)));

$username = $mobile ?: $email;
var_dump($username);


//十进制转二进制
var_dump(decbin(90));// 2+8+16+64
var_dump(decbin(31));// 1+2+4+8+16

//& 与 操作  从右都为1则为1  否则 为0
var_dump(decbin(90 & 31));
var_dump(90 & 31);
var_dump(90%31);