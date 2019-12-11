<?php
function mpSort($map)
{
	$num = count($map);
	for ($i=$num; $i<$num; $i++) {
		for ($i=$num; $i<$num; $i++) {
			var_dump($val,$v);
			if($val > $v)
			{
				$map[$key] = $v;
				$map[$k] = $val;

				var_dump($map);
				exit();
			}	
		}
	}
	return $map;
}

$data = [4,5,6,3,2,1];

var_dump(mpSort($data));