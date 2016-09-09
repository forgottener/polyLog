<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Hprose\Socket\Client;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

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
        $client = new Client('tcp://127.0.0.1:1314', false);
        $channel = ['laravel', 'thinkphp', 'symfony', 'ci', 'cakephp', 'slim'];
        $level = ["debug", "info", "notice", "warning", "error", "critical", "alert", "emergency"];
        while (true) {
            $log = Array(
                "message" => "Exception: wwww in /home/Code/illegal_lumen/app/Http/Controllers/Service/CarSyncController.php:20",
                "context" => Array(
                    "SERVER_ADDR" => "192.168.56.102"
                ),
                "level" => 400,
                "level_name" => $level[array_rand($level)],
                "channel" => $channel[array_rand($channel)],
                "extra" => Array(
                        "url" => "/illegal/car/sync/3.0",
                        "ip" => "192.168.56.1",
                        "http_method" => "GET",
                )
            );
            echo $client->polyLog($log) . PHP_EOL;
        }
    }

}
