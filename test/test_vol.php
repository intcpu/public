<?php
date_default_timezone_set("Etc/GMT");

$now    = time();
$today  = date('Y-m-d');
$yesday = date('Y-m-d',($now-86400));

$datas = [];

$file1 = fopen('/www/shell/data/min/'.$yesday.'-XBTUSD.csv','r');
$k = 0;
while ($data = fgetcsv($file1))
{
	$k++;

	if($data[0] == 'date' || $k < 1200) continue;

	$datas[] = $data;
}
fclose($file1);

$file2 = fopen('/www/shell/data/min/'.$today.'-XBTUSD.csv','r');
while ($data = fgetcsv($file2))
{
	if($data[0] == 'date') continue;

	$datas[] = $data;	
}
fclose($file2);

$duo = 0;
$kong = 0;
$last = 0;

$all = count($datas);
$num = $all - 20;
$num = max($num,0);

$news = [];


for($i = $num; $i<$all; $i++)
{
	if($i == $num) 
	{
		$last = $datas[$i][8];
		continue;
	}

	$r = $datas[$i][7] - $datas[$i][6];
	$v = $datas[$i][8] - $last;

	if($r > 0)
	{
		if($v > 0)
		{
			$kong = $kong+$r;
		}
		else
		{
			$duo = $duo-$r;
		}
	}

	if($r <= 0 )
	{
		if($v > 0)
		{
			$duo = $duo-$r;
		}
		else
		{
			$kong = $kong + $r;
		}
	}

	$duo = intval($duo);
	$kong = intval($kong);

	if(abs($duo) > 2000 && abs($kong) > 2000)
	{
		
		$news[] = $datas[$i][5].' D:'.$duo.',  K:'.$kong;
	}
	elseif(abs($duo) > 1000 && abs($kong) > 1000)
	{
		if(count($news) >= 3) break;

		$news[] = $datas[$i][5].' D:'.$duo.',  K:'.$kong;
	}
	elseif(abs($duo) > 100 && abs($kong) > 100)
	{
		if(count($news) >= 1) break;
		
		$news[] = $datas[$i][5].' D:'.$duo.',  K:'.$kong;
	}
	$last = $datas[$i][8];
}


foreach ($news as $val) {
	Curls::get('http://127.0.0.1:8188/send/group/bitbot/'.$val);
}


$json = "VOL:".intval($datas[$all-1][1])." OPEN:".intval($datas[$all-1][2])." MAX:".intval($datas[$all-1][3])." MIN:".intval($datas[$all-1][4])." CLOSE:".intval($datas[$all-1][5]);
Curls::get("http://127.0.0.1:8188/send/group/bitbot/".$json);




class Curls{

    public static function get($url, $params = false, $https = 0){
        return self::send($url, $params, 0, $https);
    }

    public static function post($url, $params = false, $https = 0){
        return self::send($url, $params,1, $https);
    }


    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    public static function send($url, $params = false, $ispost = 0, $https = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            echo "cURL Error: " . curl_error($ch);
            return false;
        }

        var_dump($response);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}