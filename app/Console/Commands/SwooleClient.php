<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

class SwooleClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'swoole异步客户端';

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
        $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞

        $client->on("connect", function($cli) {
            $cli->send("hello world\n");
        });

        $client->on("receive", function($cli, $data) {
            if (!empty($data)) {
                $cli->close();
            }
        });

        $client->on("error", function($cli){
            exit("error\n");
        });

        $client->on("close", function($cli){
            echo "connection is closed\n";
        });

        $client->connect('127.0.0.1', 9501, 0.5);
    }

}
