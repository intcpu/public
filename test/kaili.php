<?php
/**
//赌博
f* = 应该放入投注的资本比值
p = 获胜的概率
q = 失败的概率
b = 赔率
f* = (bp-q)/b = (p(b+1)-1)/b

//投资
f*,p,q同上
rW：是获胜后的净赢率
rL：是净损失率
f*=(p*rW-q*rL)/(rLrW)
*/


//20190517
//做多 胜率  %55
$rate    = 0.52;//做多胜率 (年+2 季度+3 月+2 周-3 日-2)
$is_long = 0; //做多1 做空 0
$open  = 7588;//开仓点位
$close = 6850;//平仓点位
$stop  = 7600;//止损点位
$wallet= 1100;//钱包总额
$max = 0.01;   //每次开仓总资金最多占比
$P = $is_long ? $rate : (1-$rate);
$Q = 1-$P;
$rW = abs($open - $close)/$open;
$rL = abs(($open - $stop- (0.005*$open)))/$open;

$F = (($P*$rW) - ($Q*$rL))/($rW*$rL);


$pq = $F*($wallet*$max);
$value = $pq/$open;
$lv = intval(1/$rL);
$margin = $value/$lv;
echo "开仓比例:".$F."\r\n";
echo "开仓数:".$pq."\r\n";
echo "开仓价值:".$value."\r\n";
echo "保证金：".$margin."\r\n";
echo "杠杆：".$lv."\r\n";
