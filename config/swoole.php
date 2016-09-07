<?php

return [
    //服务端配置
    'server' => [
        'host' => '0.0.0.0',
        'port' => 9501,
        'setting' => [
            'daemonize' => true,
            'reactor_num' => 8,//一般设置为CPU核数的1-4倍，在swoole中reactor_num最大不得超过CPU核数*4
            'worker_num' => 80,//业务代码是全异步非阻塞的，这里设置为CPU的1-4倍最合理;每个进程占用40M内存，那100个进程就需要占用4G内存
            "task_worker_num" => 100,
            'dispatch_mode' => 3,
            'log_file' => '/home/Code/swoole_log/swoole.log',
        ],
    ],

];