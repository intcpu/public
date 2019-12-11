<?php


var_dump(microtime());
exit();
$minid = 9;
$maxid = 23;
$task = 5;
$num = intval(($maxid - $minid)/$task);
for ($i = 0; $i < $task; $i++)
{
    $sid = ($i*$num) + $minid;
    
    $eid = ( $i == ($task -1 ) ) ? $maxid : ($sid + $num);
    
    if($eid == 0) continue;
    
    var_dump($sid,$eid);
    var_dump('-------------------------------');
}

var_dump($num);
exit();



$a = preg_match('/^[0-9\.]+$/', 0.00001);
var_dump($a);
exit();


var_dump(-21>-25);exit();


var_dump(explode('-','2018-2019',2));

$data = [];

var_dump(json_decode(''));
exit();




$path_parts = pathinfo('C:\Users\admin\Pictures\test.png');
$path_parts = mime_content_type('C:\Users\admin\Pictures\test.png');

echo $path_parts['dirname'], "\n";
echo $path_parts['basename'], "\n";
echo $path_parts['extension'], "\n";
echo $path_parts['filename'], "\n"; // since PHP 5.2.0

exit();









$str = '{}';

$data = json_decode($str,true);
var_dump($data);

foreach ($data as $val) {
    if (is_string($val)) {
        $grep = preg_match('/[\'\"\#\;\\\<\>\(\)\%\&\[\]\?\+]/', $val);

        if ($grep !== 0) {
        	preg_match_all('/[\'\"\#\;\\\<\>\(\)\%\&\[\]\?\+]/', $val, $test);
        	var_dump($val);
        	var_dump($test);
        }
    }
}
exit();


$str = 'uc_lang_code_api_cache';
$key = sprintf($str);
var_dump($key);
exit();


var_dump(json_decode('asdasd',true));

exit();

$email = 'yfx24ptex0@spam4.me';
preg_match('/^[A-Za-z0-9_.-]+@([a-zA-Z0-9_-]+)(\.[a-zA-Z0-9_-]+)+$/',$email,$matches);
var_dump($matches);