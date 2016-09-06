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
        $this->daemonize();
        $server = new Server("tcp://0.0.0.0:1314");
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
        $log['remote'] = @stream_socket_get_name($context->socket, true);
        //生成此条日志唯一编号
        $log['log_id'] = md5(generateLogid());
        $sendData = json_encode($log, JSON_UNESCAPED_UNICODE);
        $client = new \swoole_client(SWOOLE_SOCK_TCP);
        //连接到服务器
        if (!$client->connect('127.0.0.1', 9501, 0.5)) {
            throw new \Exception("Platform swoole server connect failed");
        }
        //向服务器发送数据
        if (!$client->send($sendData)) {
            throw new \Exception("Platform swoole server send content failed");
        }
        //从服务器接收数据
        $recv = $client->recv();
        if (!$recv) {
            throw new \Exception("Platform swoole server recv failed");
        }
        //关闭连接
        $client->close();
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
