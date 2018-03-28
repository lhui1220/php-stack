<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/28
 * Time: 14:46
 */

namespace PHPStack\MQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class RabbitMQTest extends TestCase
{

    public function testBasicPublish()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);
        $msg = new AMQPMessage('Hello World!');
        $channel->basic_publish($msg, '', 'hello');
        echo " [x] Sent 'Hello World!'\n";
        $channel->close();
        $connection->close();
    }

    public function testBasicConsume()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        ob_flush();
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
            ob_flush();
        };
        $channel->basic_consume('hello', '', false, true, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

}