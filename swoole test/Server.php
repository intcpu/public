<?php
include_once 'Autoload.php';

class Server
{
	private $host;
	private $port;
	private $setting = [
            'worker_num' => 3,
            'daemonize' => false,
            'log_file' => 'swoole.log',
            'max_request' => 1, //纯异步不设置，默认0
            'dispatch_mode' => 2,
            'debug_mode'=> 1
    ];
    static public $serv;

    public function __construct($host, $port, $setting = [])
    {
    	$this->host	=	$host;
    	$this->port = 	$port;
    	$this->setting = array_merge($this->setting, $setting);
    }

    public function init()
    {
        self::$serv = new swoole_server($this->host, $this->port);

        self::$serv->set($this->setting);

        self::$serv->on('Start', [$this, 'onStart']);
        self::$serv->on('WorkerStart', [$this, 'onWorkerStart']);
        self::$serv->on('ManagerStart', [$this, 'onManagerStart']);

        //进程
        self::$serv->on('Connect', [$this, 'onConnect']);
        self::$serv->on('Receive', [$this, 'onReceive']);
        self::$serv->on('PipeMessage', [$this, 'onPipeMessage']);//收到消息时

        //进程关闭时一系列操作
        self::$serv->on('Shutdown', [$this, 'onShutdown']);//work关闭主进程
        self::$serv->on('WorkerStop', [$this, 'onWorkerStop']);//work关闭
        self::$serv->on('ManagerStop', [$this, 'onManagerStop']);
        self::$serv->on('Close', [$this, 'onClose']);//关闭客户端
        
        $process = Process::init()->createProcess();
        self::$serv->addProcess($process);
        
        self::$serv->start();
    }



    public function __call($func, $param)
    {
        echo $func."\n";
    }

    public function onStart($serv)
    {
        swoole_set_process_name("swoole-main-master");

        
    }

    public function onManagerStart($serv)
    {
        swoole_set_process_name("swoole-main-manager");
    }

    public function onWorkerStart($serv, $worker_id)
    {
        swoole_set_process_name("swoole-worker-id-{$worker_id}");       
        
        echo "onWorkerStart:worker_id-{$worker_id}\n";
        echo "-------------------------------\n";
    }

    public function onPipeMessage($serv, $worker_id, $msg)
    {
         
    }

    /**
     * 清除定时器
     */
    public function clearTimer($time_id)
    {
        swoole_timer_clear($time_id);
    }


    /**
     * 根据任务数获取进程ID
     */
}


$s = new Server('127.0.0.1', 8088);
$s->init();