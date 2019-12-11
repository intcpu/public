<?php
$eo_dome = array (
  'baseInfo' => 
  array (
    'apiName' => '手机注册',
    'apiURI' => '/member/mobileSignup',
    'apiProtocol' => 0,
    'apiSuccessMock' => '',
    'apiFailureMock' => '',
    'apiRequestType' => 0,
    'apiStatus' => 0,
    'starred' => 0,
    'apiNoteType' => 0,
    'apiNoteRaw' => '',
    'apiNote' => '',
    'apiRequestParamType' => 2,
    'apiRequestRaw' => '',
    'apiUpdateTime' => '2018-08-02 16:35:03',
    'apiFailureStatusCode' => '200',
    'apiSuccessStatusCode' => '200',
    'beforeInject' => NULL,
    'afterInject' => NULL,
  ),
  'headerInfo' => 
  array (
  ),
  'mockInfo' => 
  array (
    'mockRule' => 
    array (
      0 => 
      array (
        'paramKey' => '',
        'paramType' => '0',
        '$index' => 0,
      ),
    ),
    'mockResult' => '{}',
    'mockConfig' => [
    	'rule' => '',
      'type' => 'object'
  	],
  ),
  'requestInfo' => [],
  'resultInfo' =>[],
);

$requests_info =  [
	  'paramNotNull' => '0',
      'paramType' => '0',
      'paramName' => '区号',
      'paramKey' => 'areacode',
      'paramValue' => '86',
      'paramLimit' => '',
      'paramNote' => '',
      'paramValueList' => [],
      'default' => 0,
      '$index' => 0
  ];




$post = file_get_contents('postman_collection_1.json');
$post = json_decode($post,true);

$requests = $post['requests'];

$new_eo = [];

foreach ($requests as $key => $value) {
	$new_eo[$key] = $eo_dome;

	$new_eo[$key]['baseInfo']['apiName'] = $value['name'];
	$new_eo[$key]['baseInfo']['apiURI'] = str_replace('{{url}}','',$value['url']);
	$new_eo[$key]['baseInfo']['apiURI'] = str_replace('http://47.94.138.226:8031','',$new_eo[$key]['baseInfo']['apiURI']);

	if($value['data'])
	{
		foreach ($value['data'] as $k => $v) {
			$new_eo[$key]['requestInfo'][$k] = $requests_info;
			$new_eo[$key]['requestInfo'][$k]['paramName'] = $v['description'] ?? '';
			$new_eo[$key]['requestInfo'][$k]['paramKey'] = $v['key'];
			$new_eo[$key]['requestInfo'][$k]['paramValue'] = $v['value'];
			$new_eo[$key]['requestInfo'][$k]['enabled'] = $v['enabled'];
			$new_eo[$key]['requestInfo'][$k]['$index'] = $k;

			$new_eo[$key]['requestInfo'][$k]['paramName'] = str_replace('//', '', $new_eo[$key]['requestInfo'][$k]['paramName']);
			$new_eo[$key]['requestInfo'][$k]['paramName'] = str_replace(array("\r\n", "\n", "\r"), '', $new_eo[$key]['requestInfo'][$k]['paramName']);
			$new_eo[$key]['requestInfo'][$k]['paramName'] = str_replace(array("	", " "), '', $new_eo[$key]['requestInfo'][$k]['paramName']);
		}
	}
	// if($key%5 == 0)
	// {
	// 	$eo_text = json_encode($new_eo,JSON_UNESCAPED_UNICODE);
	// 	file_put_contents('api_output_'.$key.'.export', $eo_text);
	// 	$new_eo = [];
	// }
}

$eo_text = json_encode($new_eo,JSON_UNESCAPED_UNICODE);
file_put_contents('api_output_'.$key.'.export', $eo_text);