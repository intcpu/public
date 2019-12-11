<?php

$json = 'aaaa';

var_dump(strlen($json));
$array = json_decode($json,true);
//ZLIB_ENCODING_DEFLATE
$data = gzencode(json_encode($array));
var_dump(strlen($data));
// var_dump(gzdecode($data));
?>