<?php

namespace App\Lib;


use Pusher\Pusher;

class PusherFactory
{
    public static function make()
    {
        return new Pusher(
            env("PUSHER_APP_KEY"),
            env("PUSHER_APP_SECRET"),
            env("PUSHER_APP_ID"),
            array(
                'cluster' => env("PUSHER_APP_CLUSTER"),
                'encrypted' => true,
            )
        );
    }
}
