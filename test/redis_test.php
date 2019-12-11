<?php
   $redis = new Redis();
   $redis->connect('127.0.0.1', 6379);
   echo "sucessfully\r\n";
   $redis->select('1');
   $market = $redis->get("subscribe_market_cache");
   $market = str_replace('2633.19', '5190.98', $market);
   echo $market;
?>