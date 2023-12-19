<?php

return [
    'host' => env('RMQ_HOST', '127.0.0.1'),
    'port' => env('RMQ_PORT', '5672'),
    'username' => env('RMQ_USERNAME', 'guest'),
    'password' => env('RMQ_PASSWORD', 'guest'),
    'exchange' => env('RMQ_EXCHANGE', 'transactions'),
    'inbox_queue' => env('RMQ_INBOX_QUEUE', 'inbox'),
    'handle_queue' => env('RMQ_HANDLE_QUEUE', 'handle'),
];