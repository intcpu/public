<?php
spl_autoload_register(function($class){
	
	$_ds   = DIRECTORY_SEPARATOR;
	$_path = \str_replace('\\', $_ds, $class) . '.php';
	$_dir  = __DIR__.$_ds;
	
	//设置目录
	$libs = array(
	    $_dir.'Library'.$_ds,//类库
	    $_dir.'Library'.$_ds.'PDO'.$_ds,//PDO类
	    $_dir.'ThirdParty'.$_ds,//第三方调用
	    $_dir,//当前vendor 目录
	);

	//遍历查找文件
	foreach ($libs as $lib)
	{
	    $classpath = $lib . $_path;
	    
	    if (\is_file($classpath))
	    {
	        include_once "{$classpath}";
	        return;
	    }
	}

	exit('NO CLASS '.$_path);
});