<?php

namespace teamones;

use Monolog\Logger;
use teamones\handler\TeamonesHandler;

class Log
{
    /**
     * @var array
     */
    protected static $_instance = [];

    /**
     * @param string $name
     * @return Logger;
     */
    public static function channel($name = 'default')
    {
        $configs = C('monolog');
        if (empty(static::$_instance[$name])) {
            static::$_instance[$name] = new Logger($name);
            static::$_instance[$name]->pushHandler(new TeamonesHandler($logPath, Logger::ERROR));
        };

        return static::$_instance[$name];
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::channel('default')->{$name}(... $arguments);
    }
}