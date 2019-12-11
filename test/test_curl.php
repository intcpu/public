<?php
require_once '../Autoload.php';
use Library\CurlLib;
use Library\RpcLib;

$data = CurlLib::get('http://www.baidu.com');
var_dump($data);


// $data = new RpcLib('http://www.baidu.com');
// var_dump($data);