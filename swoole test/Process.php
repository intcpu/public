<?php

class Process
{
	static private $obj;

	function __construct()
	{

	}

    static public function init()
    {
        if(!(self::$obj instanceof self))
        {
            self::$obj = new self;
        }
        return self::$obj;
    }

    public function createProcess()
    {
    	$process = new \swoole_process([$this, '__start'], false, false);
        echo "create process {$process->pid}\n";
    	return $process;
    }

    public function __start($process)
    {
        echo "process {$process->pid} start\n";
    	$this->onStart($process);
    }

    public function onStart($process)
    {
    	$process->name("swoole-process-crontab"); 
        echo "process {$process->pid} rename swoole-process-crontab start\n";
        
    }

	public function __call($func, $param)
	{
        echo "process {$process->pid} becall {$func}\n";
	}
}