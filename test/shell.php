<?php
class fileVer
{
	static private $do_file = ['.css', '.html', '.php'];
	static private $not_do_file = ['.min.css','fileVer.php'];

	static private $one_str  	= ['.ico\'', '.png\'', '.jpg\'', '.gif\''];
	static private $two_str  	= ['.ico"', '.png"', '.jpg"', '.gif"'];

	//修改版本号
	static private $do_preg = ['(\.ico\?v=)([0-9]+)' => '${1}', '(\.png\?v=)([0-9]+)' => '${1}', '(\.jpg\?v=)([0-9]+)' => '${1}', '(\.gif\?v=)([0-9]+)' => '${1}', '(\.gif\?)(t=)([0-9]+)' => '${1}v='];
	//该语句不修改版本号
	static private $not_do_str = ['=>', '{eval', '<?=', 'define(', '->assign(', '$(\'', '$("', 'var defdata ', 'str_replace'];

	static private $base_ver; //基础本版号
	static private $all_ver;

	static private function createVer(){
		self::$base_ver = date('Ymd').'00';
		self::$all_ver  = [self::$base_ver=>self::$base_ver];
	}

	static public function fileDir($base_dir){
		self::createVer();

		foreach(scandir($base_dir) as $value){
			if($value == '.' || $value == '..') continue;

			$tmp_dir = $base_dir.'/'.$value;
			if(is_dir($tmp_dir)){
				self::fileDir($tmp_dir);
			}else{
				foreach (self::$do_file as $file_val) {
					if(strpos($value,$file_val) !== false){
						$is_not = true;
						foreach (self::$not_do_file as $file_no) {
							if(strpos($value,$file_no) !== false) $is_not = false;
						}

						$tmp_file = $base_dir.'/'.$value;
						if($is_not) self::changeVer($tmp_file);
					}
				}
			}
		}
	}

	static private function changeVer($file_name){
		$str1 = [];
		$str2 = [];
		$file = fopen($file_name, 'r+');
		while(!feof($file))
		{
			$tell = ftell($file);
			$content = fgets($file);
			$is_do = true;
			foreach(self::$not_do_str as $value){
				if(strpos($content,$value) !== false){
					$is_do = false;
				}
			}

			if(!$is_do) continue;
			
			//替换版本号
			foreach(self::$do_preg as $key=>$val){
				if(preg_match('/'.$key.'/',$content,$matchs))
				{
					if(!isset(self::$all_ver[$matchs['2']]))
					{
						self::$all_ver[$matchs['2']] = self::$base_ver+1;
					}
					$str1[] = $content;
					$str2[] = preg_replace('/'.$key.'/',$val.self::$all_ver[$matchs['2']],$content);
				}
			}

			//增加单引号结尾版本号
			foreach(self::$one_str as $val){
				if(strpos($content, $val) !== false)
				{
					$str1[] = $content;
					$str2[] = str_replace($val,  trim($val,'\'').'?v='.self::$base_ver.'\'', $content);
				}
			}

			//增加双引号结尾版本号
			foreach(self::$two_str as $val){
				if(strpos($content, $val) !== false)
				{
					$str1[] = $content;
					$str2[] = str_replace($val,  trim($val,'"').'?v='.self::$base_ver.'"', $content);
				}
			}
		}
		fclose($file);

		$cont = str_replace($str1, $str2, file_get_contents($file_name));
		file_put_contents($file_name, $cont);
	}	
}

//fileVer::fileDir(__DIR__);


function delDocument($base_dir){
	foreach(scandir($base_dir) as $value){
		if($value == '.' || $value == '..' || $value == 'fileVer.php') continue;
		$tmp_file = $base_dir.'/'.$value;
		if(is_dir($tmp_file)){
			delStr($tmp_file);
		}else{
			$file_tent = '';
			$file = fopen($tmp_file, 'r+');
			while(!feof($file))
			{
				$content = fgets($file);
				$content = str_replace('document.write("', '', $content);
				$content = str_replace('");', '', $content);
				$content = str_replace('");', '', $content);
				$content = str_replace('document.write (\'', '', $content);
				$file_tent .= str_replace('\')', '', $content);
			}
			fclose($file);
			file_put_contents($tmp_file, $file_tent);
		}
	}
}

function delHtml($base_dir){
	foreach(scandir($base_dir) as $value){
		if($value == '.' || $value == '..' || $value == 'fileVer.php') continue;
		$tmp_file = $base_dir.'/'.$value;
		if(is_dir($tmp_file)){
			delStr($tmp_file);
		}else{
			$file_tent = '';
			$file = fopen($tmp_file, 'r+');
			while(!feof($file))
			{
				$content = fgets($file);
				$content = str_replace('<p align=center style=\'FONT-SIZE:13.5pt;font-family:宋体\'><b>', '', $content);
				$content = preg_replace('/<a name=(.*)>/Ui', '', $content);
				$content = str_replace('</b><p>', '', $content);
				$content = ltrim($content);
				$file_tent .= str_replace('<p>', '
', $content);
			}
			fclose($file);
			file_put_contents($tmp_file, $file_tent);
		}
	}
}


function updateName($base_dir){
	foreach(scandir($base_dir) as $value){
		if($value == '.' || $value == '..' || $value == 'fileVer.php') continue;
		$tmp_file = $base_dir.'/'.$value;
		if(is_dir($tmp_file)){
			delStr($tmp_file);
		}else{
			$file_tent = '';
			$file = fopen($tmp_file, 'r+');
			$content = fgets($file);
			fclose($file);
			$content = str_replace('?', '', $content);
			$name = trim($content);
			copy($tmp_file,'../mz1/'.$name.'.txt');
		}
	}
}

updateName(__DIR__);