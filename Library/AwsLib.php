<?php
namespace Library;

class AwsLib{

    const PUT_SERVICE = '/aws/put';
    const GET_SERVICE = '/aws/get';
    const PUBLIC_KEY = '111';
    const SYS_KEY = '222';
    const VERSION = '2.0';
    const MODULE = 'aws';

    //下载图片，支持数组
    public static function download($name) {
        $type = gettype($name);
        if($type == 'string') {            
            return self::get($name);
        } else if($type == 'array') {
            $res = [];
            foreach($name as $k=>$n) {
                $res[$k]= self::get($n);
            }
            return $res;
        }
        
        return false;
    }
    
    //获取图片
    private static function get($name) {
    
        //生成参数调用基础服务
        $serviceUrl = 'https://aaa.aaa.com'.self::GET_SERVICE;
        
        $params = array(
            'sys_key'   => self::SYS_KEY,
            'version'   => self::VERSION,
            'module'    => self::MODULE,
            'name'      => $name,
            'time'      => time(),
        );
        
        $params['sign'] = self::signature($params);
        
        if(strpos($serviceUrl,'https') === 0) {
            $res = Curls::post($serviceUrl, $params, true);
        } else {
            $res = Curls::post($serviceUrl, $params);
        }
                
        $res = json_decode($res,true);
        
        if(isset($res['code']) && $res['code'] == 200){
            return $res['data'];
        }else{
            return false;
        }
    }
    
    //上传图片,支持多图上传 $pub外网访问参数，默认为false
    public static function upload($data, $pub = false, $dir = 'upload') {
        
        if(isset($data['tmp_name'])){            
            $type = gettype($data['tmp_name']);            
            if($type == 'array') {                
                $res = $p = [];                
                foreach($data['tmp_name'] as $key=>$tmp_name) {                    
                    $p['name'] = $data['name'][$key];
                    $p['tmp_name'] = $tmp_name;
                    $res[] = self::put($p, $pub, $dir);
                }
                return $res;
            } else if($type == 'string') {
                return self::put($data, $pub, $dir);
            }
        }
        return false;        
    }
    
    private static function put($data, $pub = false, $dir = 'upload') {
        
        //生成参数调用基础服务
        $serviceUrl = env('GMS_HOST').self::PUT_SERVICE;
        $params = array(
            'sys_key'   => self::SYS_KEY,
            'version'   => self::VERSION,
            'module'    => self::MODULE,
            'file'      => new \CurlFile($data['tmp_name']),
            'name'      => $data['name'],
            'pub'       => $pub,
            'dir'       => $dir,
            'time'      => time(),
        );
        
        $params['sign'] = self::signature($params);

        if(strpos($serviceUrl,'https') === 0) {
            $res = Curl::post($serviceUrl, $params, true);
        } else {
            $res = Curl::post($serviceUrl, $params);
        }

        $res = json_decode($res,true);
        
        if(isset($res['code']) && $res['code'] == 200){
            return $res['data'];
        }else{
            return false;
        }
    }
    
    private static function signature($data){
        $params = [
            'sys_key' => $data['sys_key'],
            'version' => $data['version'],
            'time' => $data['time'],
            'module' => $data['module'],
            'public_key' => self::PUBLIC_KEY,
        ];

        ksort($params);
        return md5(implode(',',$params));

    }
}