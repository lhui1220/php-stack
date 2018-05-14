<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/16
 * Time: 10:53
 */

//ini_set("max_execution_time", "0");

require __DIR__.'/vendor/autoload.php';

require __DIR__.'/routes.php';

define('ROOT_PATH', dirname(__FILE__).'/');

$resp = \PHPStack\Framework\Router::dispatch($_SERVER['REQUEST_URI']);

echo $resp;

