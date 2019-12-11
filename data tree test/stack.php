<?php
class stack
{

	private static $hand = 0;
	private static $all  = [];

	public static function push($data)
	{
		self::$all[self::$hand++] = $data;
		return true;
	}

	public static function pop()
	{
		$data = self::$all[--self::$hand];
		unset(self::$all[self::$hand]);
		return $data;
	}

	public static function getData()
	{
		return self::$all;
	}
}

if(!isset(get_included_files()[1]))
{
	var_dump(stack::push(1));
	var_dump(stack::push(2));
	var_dump(stack::push(3));
	var_dump(stack::push(4));


	var_dump(stack::pop());
	var_dump(stack::getData());
	var_dump(stack::pop());

	var_dump(stack::getData());
}