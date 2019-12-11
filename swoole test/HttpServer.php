<?php
include_once 'Autoload.php';

class HttpServer
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
        self::$serv = new swoole_http_server($this->host, $this->port);

        self::$serv->set($this->setting);

        self::$serv->on('Start', [$this, 'onStart']);
        self::$serv->on('WorkerStart', [$this, 'onWorkerStart']);
        self::$serv->on('ManagerStart', [$this, 'onManagerStart']);


        self::$serv->on('request',[$this, 'onRequest']);

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
        $worker_id = self::$serv->worker_id;
        unset($param[0]);
        $param = json_encode($param);
        echo "worker_id-{$worker_id} call {$func} param : {$param}\n";
    }

    public function onStart($serv)
    {
        swoole_set_process_name("swoole-main-master");
        echo "swoole-main-master start\n";
    }

    public function onManagerStart($serv)
    {
        swoole_set_process_name("swoole-main-manager");
        echo "swoole-main-manager start\n";
    }

    public function onWorkerStart($serv, $worker_id)
    {
        swoole_set_process_name("swoole-worker-id-{$worker_id}");       
        echo "swoole-worker-id-{$worker_id} start\n";
    }

    public function onConnect($serv, $fd_id, $reactor_id)
    {     
        //echo "user fd_id-{$fd_id} to reactor_id-{$reactor_id} connect\n";
    }

    public function onWorkerStop($serv, $worker_id)
    {
        //echo "worker_id-{$worker_id} stop\n";
    }

    public function onClose($serv, $fd_id, $reactor_id)
    {     
        //echo "user fd_id-{$fd_id} to reactor_id-{$reactor_id} close\n";
    }   

    public function onRequest($request, $response)
    {
        $response->end($request->getData());
    }

    public function onPipeMessage($serv, $worker_id, $msg)
    {
        $worker_id = self::$serv->worker_id;
        echo "worker_id-{$worker_id} send to my_id-{$worker_id} msg:{$msg} ";
    }

    /**
     * 清除定时器
     */
    public function clearTimer($time_id)
    {
        swoole_timer_clear($time_id);
    }
}


$s = new HttpServer('0.0.0.0', 8080);
$s->init();