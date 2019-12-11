<?php
class PhpRedis
{
  static public  $redis;

  static public function init()
  {
      if(!self::$redis)
      {
        self::$redis = new redis();  
        self::$redis->connect('127.0.0.1', 6379);
        self::$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
      }
  }

  static public function __callStatic($func, $param)
  {
      return call_user_func_array([self::$redis,$func],$param);
  }
}