<?php
class Verify
{
    private static $private_key = '';
    private static $public_key = '';
    
    /**
     * 签名
     *
     * @param array $data 需要签名的内容
     * @param string $rsaPrivateKey rsa私钥
     * @return string 生成的签名
     */
    public static function genSign(array $data)
    {
        //1. 生成待签名文本
        $signReq = self::genSignReq($data);
        //2. 对摘要进行签名
        $signRes = null;
        $key = openssl_get_privatekey(self::$private_key) or exit('openssl_get_privatekey failed: '.openssl_error_string());
        openssl_sign($signReq, $signRes, $key) or exit('openssl_sign failed: '.openssl_error_string());
        openssl_free_key($key);
        //3. base64编码
        $sign = base64_encode($signRes);
        return $sign;
    }
    
    /**
     * @param array $data 待验签的文本
     * @param $rsaPublicKey 公钥
     * @return bool
     */
    public static function verifySign(array $data)
    {
        if(!isset($data['sign'])){
            return false;
        }
        $sign = base64_decode($data['sign']);
        unset($data['sign']); // 验签前需要把sign排除
        //1. 生成待签名文本
        $signReq = self::genSignReq($data);
        //2. 验签
        $key = openssl_get_publickey(self::$public_key);
        $result = openssl_verify($signReq, $sign, $key);
        openssl_free_key($key);
        if($result == -1){
            exit('openssl_verify failed: '.openssl_error_string());
        }
        return $result==1;
    }
    
    /**
     * 生成待签名文本
     * @param array $data
     */
    private static function genSignReq(array $data)
    {
        //1. 先将参数按参数名的字典序升序进行排序
        $signReq = [];  //待签名字符串
        ksort($data);
        //2. 将数组拼成字符串。注意, 对于json中的null、bool类型, 需要转换成约定好的文本
        foreach ($data as $k => $v) {
            if($v === null) { // null 转换成空字符串
                $v = '';
            }elseif ($v === true) { // true 转换文本'1'
                $v = '1';
            }elseif ($v === false) { // true 转换成文本'0'
                $v = '0';
            }
            //为key/value对生成一个key=value格式的字符串，并拼接到待签名字符串后面
            $signReq[] = "$k=$v";
        }
        return implode('&', $signReq);
    }
}


$sign = Verify::genSign(['a'=>'b']);

var_dump($sign);
$v = Verify::verifySign(['a'=>'b','sign'=>$sign]);
var_dump($v);
