<?php

return [
    'default' => [
        'host' => env('BEANSTALK_HOST', '127.0.0.1'),
        'port' => (int) env('BEANSTALK_PORT', 11300),
        'connect_timeout' => 3,  // Connect timeout, -1 means never timeout.
        'timeout' => 3,         // Read, write timeout, -1 means never timeout.
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) env('BEANSTALK_MAX_IDLE_TIME', 60),
        ],
    ],
];