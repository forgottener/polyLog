<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Hprose\Socket\Server;
use Log;

class HproseServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hprose:server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'hprose服务端(RPC)';

    protected $swClient;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //是否开启守护进程
        if (config('hprose.server.setting.daemonize')) {
            $this->daemonize();
        }
        $this->swClient = new \swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
        //连接到swoole服务
        if (!$this->swClient->connect(config('swoole.client.host', '127.0.0.1'), config('swoole.client.port', 9501), config('swoole.client.time_out', 1))) {
            Log::alert("Platform swoole server connect failed");
            throw new \Exception("Platform swoole server connect failed");
        }
        $server = new Server("tcp://" . config('hprose.server.host', '0.0.0.0') . ":" . config('hprose.server.port', 1314));
        $server->addFunction([$this, 'swooleClient'], 'polyLog', ["passContext" => true]);
        $server->start();
    }

    /**
     * 发起swoole客户端,进行记录日志任务
     * @param $log
     * @param $context
     * @return string
     */
    public function swooleClient($log, $context)
    {
        if (!is_array($log)) {
            $log = json_decode($log, true);
        }
        if (!isset($log['channel']) || !isset($log['message'])) {
            throw new \Exception('日志缺少关键参数channel or message');
        }
        //获取上报服务器client的tcp连接信息
        $remote = @stream_socket_get_name($context->socket, true);
        if ($remote) {
            list($log['remote_ip'], $log['remote_port']) = explode(":", $remote);
        }
        if (!isset($log['log_id']) || empty($log['log_id'])) {
            //生成此条日志唯一编号
            $log['log_id'] = md5(generateLogid());
        }
        $sendData = json_encode($log, JSON_UNESCAPED_UNICODE);
        //向服务器发送数据
        if (!$this->swClient->send($sendData)) {
            Log::alert("Platform swoole server send content failed");
            throw new \Exception("Platform swoole server send content failed");
        }
        //从服务器接收数据
        $recv = $this->swClient->recv();
        if (!$recv) {
            Log::alert("Platform swoole server recv failed");
            throw new \Exception("Platform swoole server recv failed");
        }
        return $log['log_id'];
    }

    public function daemonize()
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            die("fork(1) failed!\n");
        } elseif ($pid > 0) {
            //让由用户启动的进程退出
            exit(0);
        }

        //建立一个有别于终端的新session以脱离终端
        posix_setsid();

        $pid = pcntl_fork();
        if ($pid == -1) {
            die("fork(2) failed!\n");
        } elseif ($pid > 0) {
            //父进程退出, 剩下子进程成为最终的独立进程
            exit(0);
        }
    }
}
