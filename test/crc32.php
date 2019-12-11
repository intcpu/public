<?php

#define ngx_hash(key, c)   ((u_int) key * 31 c)

function ngx_simple_hash($data)
{
	$data = crc32($data);
    $key = 0;
    $len = strlen($data);
    
    for ($i = 1; $i < $len; $i++) {
        $key  = ($key*31)+$data[$i];
    }
    return $key%3;
}


//2082590 2
//2552752 2
//1413532 2
//2627453 0
//2559154 0
//2431947 0
//2627454 1
//2687692 1
//2659723 1
$a = ngx_simple_hash('2431947');
var_dump($a);
$a = ngx_simple_hash('2559154');
var_dump($a);
$a = ngx_simple_hash('2637922');
var_dump($a);
$a = ngx_simple_hash('2627454');
var_dump($a);