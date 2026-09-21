<?php
namespace App\Middleware;

class GuestMiddleware
{
    public static function handle()
    {
        return true;
    }
}
