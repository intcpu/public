<?php
function is_normal_str($str)
{
	preg_match_all('/./u', $str,$matches);
	foreach ($matches[0] as $key => $val) {
		if(strlen($val) > 3)
		{
			return false;
		}
		$is_match = preg_match('/[\x{3200}-\x{33FF}\x{2000}-\x{303F}\x{FE00}-\x{FFFF}]/u', $val);
		if ($is_match) {
			return false;
		}
	}
	return true;
}
var_dump(is_normal_str('祝你413500@qq.comภาษาเวียดนามベトナム語Tiếng Việt越南語Vietnamese월남어 الفيتنامية '));
exit();


$nickName = '祝你🐖413500@qq.comภาษาเวียดนามベトナム語Tiếng Việt越南語Vietnamese월남어 الفيتنامية ';
function mb_str_split($str)
{
    return preg_split('/(?<!^)(?!$)/u', $str);
}

$str = '🐖㊗';
var_dump(mb_strlen($str));

$is_true = preg_match("/^[0-9a-zA-Z\x80-\xff. ]{1,60}$/", $str);
var_dump($is_true);


//$nickNameMap = mb_str_split($nickName);





exit();



$mobile = '12345@123';
$is_true = preg_match('/[0-9]{5,11}/',$mobile);

var_dump($is_true);
EXIT();


$str = '都是';
$a = preg_match("/^[0-9a-zA-Z\x80-\xff\ \_\-\:\=\.\/]+$/", $str);
var_dump($a);
exit();

$a = ['sharklasers','guerrillamail','guerrillamailblock','spam4','pokemail','yopmail','0box','contbay','damnthespam','kurzepost','objectmail','proxymail','rcpt','trash-mail','trashmail','wegwerfmail','grr'];
$b = '1@trashmail-.com';

$str = 'uc/20180719/11111-7.png';
$str = '무슨무슨무무슨무슨무무슨무슨무무슨무슨무. ';
$is_true = preg_match("/^[0-9a-zA-Z\x80-\xff. ]+$/", $str);
var_dump($is_true);
exit();

$value  = "'piece1 piece2' piece3 piece4 piece5 piece6'";


		if (strpbrk($value[0], '"\'') !== false)
		{
			// value starts with a quote
			$quote = $value[0];
			$regexPattern = sprintf(
					'/^
					%1$s          # match a quote at the start of the value
					(             # capturing sub-pattern used
								  (?:          # we do not need to capture this
								   [^%1$s\\\\] # any character other than a quote or backslash
								   |\\\\\\\\   # or two backslashes together
								   |\\\\%1$s   # or an escaped quote e.g \"
								  )*           # as many characters that match the previous rules
					)             # end of the capturing sub-pattern
					%1$s          # and the closing quote
					.*$           # and discard any string after the closing quote
					/mx', $quote
			);
			
			$value = preg_replace($regexPattern, '$1', $value);
			$value = str_replace("\\$quote", $quote, $value);
			$value = str_replace('\\\\', '\\', $value);
			var_dump($value);
		}
		else
		{
			$parts = explode(' #', $value, 2);

			$value = trim($parts[0]);

			// Unquoted values cannot contain whitespace
			if (preg_match('/\s+/', $value) > 0)
			{
				
			}
		}

var_dump($value);
exit();

$str = md5(md5('1111@qq.com') . '111' . md5('111'));

var_dump($str);
exit();


$str = 'aaaaa';
var_dump(unserialize(base64_decode($str)));
exit();


function array_iconv($data, $output = 'utf-8') {
  $encode_arr = array('UTF-8','ASCII','GBK','GB2312','BIG5','JIS','eucjp-win','sjis-win','EUC-JP');
  $encoded = mb_detect_encoding($data, $encode_arr);
  if (!is_array($data)) {
    return mb_convert_encoding($data, $output, $encoded);
  }
  else {
    foreach ($data as $key=>$val) {
      $key = array_iconv($key, $output);
      if(is_array($val)) {
        $data[$key] = array_iconv($val, $output);
      } else {
      	$data[$key] = mb_convert_encoding($data, $output, $encoded);
      }
    }
  	return $data;
  }
}


//str = trim(json_encode('fdfd㊗'),'"');



$str = 'fdfd㊗祝你🐖';

$str = '🐖';

$str = '－＋＝';


preg_match_all('/./u', $str,$matches);
foreach ($matches[0] as $key => $val) {
	var_dump(strlen($val));
	if(strlen($val) > 3)
	{
		return false;
	}
	$is_match = preg_match('/[\x{3200}-\x{32FF}]/u', $val);
	var_dump($is_match);
	if ($is_match) {
		return false;
	}
}

exit();
var_dump($str);
exit();
$c = unpack('C*', '㊗');
var_dump($c);

$c = unpack('C*', '祝');
var_dump($c);


exit();

$str = strlen('㊗');
var_dump($str);
$str = strlen('🐖');
var_dump($str);
$str = strlen('祝');
var_dump($str);
$str = strlen('🌸');
var_dump($str);

exit();

// $a = '㊗';
// $a = '主';
// $a = '🐖';


$b = iconv("UTF-8","unicode",$a);
var_dump($b);
exit();
$b = unicode_encode($a);
var_dump($b);
exit();

$b = pack('P*',$a);
var_dump($b);
$c = unpack('P*', $b);
var_dump($c);
exit();




$a = '84973!@^&**&*^&&*5{}{SSFDSDFDFHsdhfsdhf!@$#%$^5AAAA你平凡㊗️㊗️';
preg_match_all('/./u', $a, $matches);
foreach ($matches[0] as $key => $val) {
	$v = unpack('C*', $val);
	var_dump($v);
}


























$pwd = md5('123456');
var_dump($pwd);
exit();


















$old = '{"vtc": { "btc": "0.00000658", "usd": "0.04999780", "cny": "0.34076001" },    "dkkt": { "btc": "0.00002047", "usd": "0.1603", "cny": "1.0082" },   "woc": { "btc": "0.00000368", "usd": "0.02796229", "cny": "0.19057702" },  "ecom": { "btc": "0.00000009", "usd": "0.00068386", "cny": "0.00466085" },  "gbc": { "btc": "0.00000017", "usd": "0.00129173", "cny": "0.00880383" },  "vct": { "btc": "0.00000398", "usd": "0.03024183", "cny": "0.20611319" },  "sexc": { "btc": "0.00000065", "usd": "0.00493899", "cny": "0.03366170" }, "nxct": { "btc": "0.00000021", "usd": "0.00159567", "cny": "0.01087531" },  "xct": { "btc": "0.00000004", "usd": "0.00030393", "cny": "0.00207148" },  "wwb": { "btc": "0.00001405", "usd": "0.10675822", "cny": "0.72761066" }, "swtc": { "btc": "0.00000025", "usd": "0.00189961", "cny": "0.01294680" }, "moac": { "btc": "0.00041904", "usd": "3.18405448", "cny": "21.70092333" },   "rntb": { "btc": "0.00000301", "usd": "0.02287133", "cny": "0.15587958" },  "pch": { "btc": "0.00000360", "usd": "0.02735442", "cny": "0.18643404" }, "fbee": { "btc": "0.00000074", "usd": "0.00562285", "cny": "0.03832255" }, "dsg": { "btc": "0.00865000", "usd": "65.72659250", "cny": "447.95959053" }, "pps": { "btc": "0.00000407", "usd": "0.03092569", "cny": "0.21077405" }, "esn": { "btc": "0.00001243", "usd": "0.09444873", "cny": "0.64371534" }, "bstk": { "btc": "0.00000034", "usd": "0.00258347", "cny": "0.01760766" }, "cpx": { "btc": "0.00000502", "usd": "0.03814421", "cny": "0.25997192" }, "rrc": { "btc": "0.00000538", "usd": "0.04087966", "cny": "0.27861532" }, "iov": { "btc": "0.00000118", "usd": "0.00896617", "cny": "0.06110893" },  "npxs": { "btc": "0.00000031", "usd": "0.00235551", "cny": "0.01605404" }, "gus": { "btc": "0.00000420", "usd": "0.03191349", "cny": "0.21750639" },  "sctk": { "btc": "0.00000046", "usd": "0.00349528", "cny": "0.02382212" }, "esa": { "btc": "0.00001045", "usd": "0.07940380", "cny": "0.54117661" },  "ext": { "btc": "0.00000020", "usd": "0.00151969", "cny": "0.01035744" }, "ply": { "btc": "0.00001374", "usd": "0.10440270", "cny": "0.71155662" },  "btn": { "btc": "0.00000900", "usd": "0.06838605", "cny": "0.46608512" }, "ixt": { "btc": "0.00002870", "usd": "0.21807551", "cny": "1.48629367" }, "nam": { "btc": "0.00000021", "usd": "0.00159567", "cny": "0.01087531" }, "zpr": { "btc": "0.00000153", "usd": "0.01162562", "cny": "0.07923447" },  "del": { "btc": "0.00000392", "usd": "0.02978592", "cny": "0.20300596" }}';

$new = '{"wwb": { "btc": "0.00001405", "usd": "0.10670743", "cny": "0.72599941" }, "fbee": { "btc": "0.00000074", "usd": "0.00562017", "cny": "0.03823769" },  "pps": { "btc": "0.00000407", "usd": "0.03091097", "cny": "0.21030730" }, "cpx": { "btc": "0.00000502", "usd": "0.03812607", "cny": "0.25939623" }, "pch": { "btc": "0.00000360", "usd": "0.02734140", "cny": "0.18602120" },  "gus": { "btc": "0.00000420", "usd": "0.03189830", "cny": "0.21702473" }, "rrc": { "btc": "0.00000538", "usd": "0.04086021", "cny": "0.27799835" }, "bstk": { "btc": "0.00000034", "usd": "0.00258224", "cny": "0.01756866" }, "npxs": { "btc": "0.00000031", "usd": "0.00235439", "cny": "0.01601849" }, "iov": { "btc": "0.00000118", "usd": "0.00896190", "cny": "0.06097361" }, "dsg": { "btc": "0.00865000", "usd": "65.69532275", "cny": "446.96761113" }, "ply": { "btc": "0.00001374", "usd": "0.10435303", "cny": "0.70998092" }, "ext": { "btc": "0.00000020", "usd": "0.00151896", "cny": "0.01033451" }, "sctk": { "btc": "0.00000046", "usd": "0.00349362", "cny": "0.02376937" },  "esn": { "btc": "0.00001243", "usd": "0.09440379", "cny": "0.64228987" }, "ixt": { "btc": "0.00002870", "usd": "0.21797176", "cny": "1.48300236" },  "btn": { "btc": "0.00000900", "usd": "0.06835351", "cny": "0.46505300" }, "zpr": { "btc": "0.00000153", "usd": "0.01162009", "cny": "0.07905901" }, "del": { "btc": "0.00000392", "usd": "0.02977175", "cny": "0.20255642" }, "nam": { "btc": "0.00000021", "usd": "0.00159491", "cny": "0.01085123" }, "woc": { "btc": "0.00000369", "usd": "0.02803577", "cny": "0.19073017" }, "ecom": { "btc": "0.00000009", "usd": "0.00074750", "cny": "0.00508536" }, "gbc": { "btc": "0.00000017", "usd": "0.00134636", "cny": "0.00915944" }, "vct": { "btc": "0.00000399", "usd": "0.03035474", "cny": "0.20650636" }, "sexc": { "btc": "0.00000065", "usd": "0.00496922", "cny": "0.03380613" }, "vtc": { "btc": "0.00000663", "usd": "0.05041039", "cny": "0.34293220" }, "nxct": { "btc": "0.00000021", "usd": "0.00163517", "cny": "0.01112424" }, "xct": { "btc": "0.00000004", "usd": "0.00034402", "cny": "0.00234042" }, "swtc": { "btc": "0.00000025", "usd": "0.00195371", "cny": "0.01329130" }, "moac": { "btc": "0.00041998", "usd": "3.18969042", "cny": "21.69978282" }, "rntb": { "btc": "0.00000301", "usd": "0.02293488", "cny": "0.15602831" },  "esa": { "btc": "0.00001047", "usd": "0.07955856", "cny": "0.54124489" },  "dkkt": { "btc": "0", "usd": "0", "cny": "0" }}';


$new = json_decode($new,true);
$old = json_decode($old,true);

$data = [];
foreach ($old as $key => $val) {
	$data[$key][0] = $val;
	$data[$key][1] = $new[$key];
}

exit(json_encode($data));











$data = ['status' => -110136];
$replace = 3;
$data['msg']  = 'Sms authentication code was incorrectly entered %s times. The account will be frozen for 2 hours when 5 times of error.';

if (!empty($replace))
{
	if(is_array($replace))
	{
	    array_unshift($replace,$data['msg']);
	}
	else
	{
	    $replace =[$data['msg'],$replace];
	}
	$data['msg'] = call_user_func_array('sprintf',$replace);
}

echo json_encode($data);

try{
	
}
catch(\Throwable | \Error | \Exception $e){
    echo 'hello error';
}
