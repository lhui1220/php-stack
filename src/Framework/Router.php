<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/20
 * Time: 10:43
 */

namespace PHPStack\Framework;

class Router
{
    private static $requestMapping = [];

    private static $requestHandlerMapping = [];

    public static function register($uri, $action)
    {
        self::$requestMapping[$uri] = $action;
    }

    public static function dispatch($uri)
    {
        $handler = self::getOrCreate($uri);

        return $handler();
    }

    private static function getOrCreate($uri)
    {
        if (!isset(self::$requestMapping[$uri])) {
            return null;
        }
        if (isset(self::$requestHandlerMapping[$uri])) {
            return self::$requestHandlerMapping[$uri];
        }
        $action = self::$requestMapping[$uri];

        list($handler,$action) = explode('@',$action);

        $clazz = new \ReflectionClass($handler);
        $instance = $clazz->newInstance();

        $method = new \ReflectionMethod($instance,$action);

        $handler = function () use ($instance, $method) {
            return $method->invoke($instance);
        };

        self::$requestHandlerMapping[$uri] = $handler;

        return $handler;
    }

}