<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Api\LogDetails;

class SwooleServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'swoole服务端异步Task';

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
        $serv = new \swoole_server(config('swoole.server.host', '127.0.0.1'), config('swoole.server.port', 9501));

        //设置异步任务的工作进程
        $serv->set(config('swoole.server.setting'));

        $serv->on('workerstart', function($serv, $id) {
            $serv->logDetails = new LogDetails();
        });

        $serv->on('receive', function($serv, $fd, $from_id, $data) {
            //投递异步任务
            $serv->task($data);
            //立即通知客户端
            $serv->send($fd, "Start Task" . PHP_EOL);
        });

        //处理异步任务
        $serv->on('task', function ($serv, $task_id, $from_id, $data) {
            $logDetail = json_decode($data, true);
            $LogDetails = $serv->logDetails;
            $LogDetails::create([
                'channel' => isset($logDetail['channel']) ? $logDetail['channel'] : 'default',
                'level' => isset($logDetail['level']) ? $logDetail['level'] : 0,
                'level_name' => isset($logDetail['level_name']) ? strtolower($logDetail['level_name']) : '',
                'message' => isset($logDetail['message']) ? $logDetail['message'] : '',
                'remote_ip' => isset($logDetail['remote_ip']) ? $logDetail['remote_ip'] : '',
                'remote_port' => isset($logDetail['remote_port']) ? $logDetail['remote_port'] : '',
                'from_id' => $from_id,
                'task_id' => $task_id,
                'log_id' => isset($logDetail['log_id']) ? $logDetail['log_id'] : '',
                'data' => $data
            ]);
            //返回任务执行的结果
            $serv->finish("OK");
        });

        //处理异步任务的结果
        $serv->on('finish', function ($serv, $task_id, $data) {

        });

        $serv->start();
    }

}
