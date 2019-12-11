<?php
namespace Library;

class CurlLib
{
	private static $url = ''; // 访问的url
	private static $oriUrl = ''; // referer url
	private static $data = array(); // 可能发出的数据 post,put
	private static $method = 'get'; // 访问方式，默认是GET请求
	public  static $header = [];
	public  static $proxyHost;
	public  static $proxyPort;

	static public function __callStatic($func, $args)
	{
		self::$method = strtolower($func);

		if (!in_array(self::$method,['get', 'post', 'put', 'delete']))
		{
			exit('error request method type!');
		}

		if(!isset($args[0]))
		{
			exit('url can not be null');
		}

		$args[1] = isset($args[1]) ? $args[1] : [];

		return self::send($args[0], $args[1]);
	}

	static private function send($url, $data)
	{
		self::$url = $url;
		$urlArr = parse_url($url);
		self::$oriUrl = $urlArr['scheme'] .'://'. $urlArr['host'];
		self::$data = $data;

		$func = self::$method . 'Request';
		return self::$func(self::$url);
	}

	/**
	 * 基础发起curl请求函数
	 * @param int $is_post 是否是post请求
	 */
	static private  function doRequest($is_post = 0)
	{
		$ch = curl_init();//初始化curl
		curl_setopt($ch, CURLOPT_URL, self::$url);//抓取指定网页
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_REFERER, self::$oriUrl); //来源一定要设置成来自本站

		if(!empty(self::$header)) curl_setopt($ch, CURLOPT_HTTPHEADER, self::$header); //设置header

		curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置超时时间
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果返回为字符串 非直接输出到屏幕上
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
		curl_setopt($ch, CURLOPT_MAXREDIRS, 7); //HTTp定向级别
		curl_setopt($ch, CURLINFO_HEADER_OUT, false); 
		curl_setopt($ch, CURLOPT_HEADER, false);

		if (!empty(self::$data)) {

			if(is_array(self::$data)) self::$data = self::dealPostData(self::$data);

			curl_setopt($ch, CURLOPT_POSTFIELDS, self::$data);
		}

		//设置代理
		if(self::$proxyHost && self::$proxyPort)
		{
			curl_setopt($ch, CURLOPT_PROXY, self::$proxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, self::$proxyPort);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts	
		}

		switch($is_post) {
			case 0:
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			break;
			case 1:
			curl_setopt($ch, CURLOPT_POST, true);
			break;
			case 2:
			curl_setopt($ch, CURLOPT_PUT, true);
			break;
			case 3:
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			break;
		}	

		$data = curl_exec($ch);//运行curl
        $status = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $err = curl_error($ch);;
		curl_close($ch);
		return $data;
	}

	/**
	 * 发起get请求
	 */
	static private function getRequest()
	{
		return self::doRequest(0);
	}

	/**
	 * 发起post请求
	 */
	static private function postRequest()
	{
		return self::doRequest(1);
	}

	/**
	 * 处理发起非get请求的传输数据
	 * 
	 * @param array $postData
	 */
	static private function dealPostData($postData)
	{
		if (!is_array($postData)) exit('post data should be array');
		$o = '';
		foreach ($postData as $k => $v) {
		    $o .= "$k=" . urlencode($v) . "&";
		}
		$postData = substr($o, 0, -1);
		return $postData;
	}

	/**
	 * 发起put请求
	 */
	static private function putRequest($param)
	{
		return self::doRequest(2);
	}

	/**
	 * 发起delete请求
	 */
	static private function deleteRequest($param)
	{
		return self::doRequest(3);
	}
    
}