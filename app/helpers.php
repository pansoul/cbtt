<?php

use App\Flash\Flash;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

if (!function_exists('flash')) {
    function flash(): Flash
    {
        return app(Flash::class);
    }
}

if (!function_exists('cbttlog')) {
    function cbttlog(): LoggerInterface
    {
        return Log::channel('cbtt');
    }
}