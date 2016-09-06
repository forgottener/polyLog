<?php

return [
    //服务端配置
    'server' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'setting' => [
            'daemonize' => true,
            'reactor_num' => 2,
            'worker_num' => 4,
            "task_worker_num" => 4,
            'max_request' => 10000,
            'dispatch_mode' => 3,
            'log_file' => '/home/Code/swoole_log/swoole.log',
        ],
    ],

];