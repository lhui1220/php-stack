<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/20
 * Time: 10:42
 */

use PHPStack\Framework\Router;

Router::register('/redis/setex','PHPStack\Redis\RedisSkill@saveTTLKey');

Router::register('/redis/hop','PHPStack\Redis\RedisSkill@hop');
