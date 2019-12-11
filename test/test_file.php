<?php 
require_once '../Autoload.php';
use Library\AwsLib;
use Library\Curls;

$file = AwsLib::download('upload/aaaaa.jpeg');
var_dump($file);