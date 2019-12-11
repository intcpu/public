<?php 
/**
 * 
一个好的随机数生成系统能确保生成质量适合的随机数。为了检验质量，需要运行一系列的统计试验。此处，暂不深入讨论复杂的统计话题，将已知的行为与随机数生成器的结果进行比较，有助于质量评估。

一个简单的测试方法是掷骰游戏。假设投掷一次，投出6的概率是1/6。如果同时投掷三个骰子，投100次，投得零次、一次、两次及三次6的次数大概是：

0 次6 = 57.9 次
1 次6 = 34.7 次
2 次6 = 6.9 次
3 次6 = 0.5 次

*/


//以下是骰子投掷100万次的代码：

$times   = 1000000;
$test_3  = 0.005;
$e       = $times*$test_3;

$result  = [0,0,0,0,0,0,0];
$data 	 = [0,0,0,0,0,0,0];
for ($i=0; $i<$times; $i++){

	$data[roll()] += 1;  	//每个骰点出现次数


	$dieRoll = [0,0,0,0,0,0,0];	//把6的统计次数设为0
	$dieRoll[roll()] += 1; 		//first die
	$dieRoll[roll()] += 1; 		//second die
	$dieRoll[roll()] += 1; 		//third die

	$result[$dieRoll[6]] += 1; //counts the sixes
}
function roll()
{
	//return rand(1,6);
	return random_int(1,6);
}


$a = ($result[3] - $e) / sqrt($e);
var_dump($a);

var_dump($data);
var_dump($result[3]);