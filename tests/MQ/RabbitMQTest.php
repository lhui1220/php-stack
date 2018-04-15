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
use PhpAmqpLib\Wire\AMQPTable;
use PHPStack\Common\StringUtils;
use PHPUnit\Framework\TestCase;

class RabbitMQTest extends TestCase
{
    private static $DELAY_QUEUE = 'delay_queue';
    private static $ORDER_QUEUE = 'order_queue';
    private static $EX_ORDER = 'ex_order';
    private static $DELAY_ROUTING_KEY = 'timeout_order';

    private $users;

    protected function setUp()
    {
        ob_end_clean(); //关闭输出缓冲区，支持立即输出

        $this->users = ['xiaoming','xiaohong','xiaofang'];
    }

    /**
     * @group basic
     */
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

    /**
     * @group basic
     */
    public function testBasicConsume()
    {

        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };
        $channel->basic_consume('hello', '', false, true, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * @group work queue
     */
    public function testNewTask()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->queue_declare('tasks', false,
            true, //开启队列持久化
            false, //true表示与消费者断连的时候删除队列
            false);
        $msg = new AMQPMessage(StringUtils::randStr(mt_rand(3,30)),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT] //开启消息持久化
        );
        $channel->basic_publish($msg, '', 'tasks');
        echo " [x] Sent ". $msg->body ."\n";
        $channel->close();
        $connection->close();
    }

    /**
     * @group work queue
     *
     * 使用工作队列注意事项:
     * 1.消息确认(防止程序崩溃时消息丢失)
     * 2.消息持久化(防止RabbitMQ停止或崩溃时消息丢失）
     * 3.公平调度(防止负载不均衡)
     */
    public function testWorker()
    {

        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->queue_declare('tasks', false,
            true, //开启队列持久化
            false, false);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, " sleep ", strlen($msg->body), "s\n";
            sleep(strlen($msg->body));
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            echo " [x] Ack ",$msg->body, "\n";
        };
        $channel->basic_qos(null,1,null);
        $channel->basic_consume(
            'tasks',
            '',
            false,
            false, //响应ACK,防止消息丢失
            false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * @group PUB/SUB
     */
    public function testPublish()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->exchange_declare('posts','fanout');
        $post = StringUtils::randStr(mt_rand(3,30));
        $msg = new AMQPMessage('NEW post:' . $post);
        $channel->basic_publish($msg, 'posts');
        echo " [x] Sent '$post'\n";
        $channel->close();
        $connection->close();
    }

    /**
     * @group  PUB/SUB
     */
    public function testSubscribe()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->exchange_declare('posts','fanout');
        $result = $channel->queue_declare('', false, false, false, false);
        $queue = $result[0];
        $channel->queue_bind($queue, 'posts');

        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };
        $channel->basic_consume($queue, '', false, true, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * @group routing
     */
    public function testRoutingPub()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->exchange_declare('user_posts','direct');
        //模拟用户发帖
        $post = StringUtils::randStr(mt_rand(3,30));
        $user = $this->users[mt_rand(0,count($this->users)-1)];
        $msg = new AMQPMessage("$user:$post");

        $channel->basic_publish($msg, 'user_posts' ,$user);
        echo " [x] Sent $user:$post\n";
        $channel->close();
        $connection->close();
    }

    /**
     * @group routing
     */
    public function testRoutingSub()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->exchange_declare('user_posts','direct');
        $result = $channel->queue_declare('', false, false, false, false);
        $queue = $result[0];
        //模拟订阅关注用户的帖子
        $user = $this->users[mt_rand(0,count($this->users)-1)];
        $channel->queue_bind($queue, 'user_posts', $user);

        echo " [*] Waiting for $user 's posts. To exit press CTRL+C", "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
        };
        $channel->basic_consume($queue, '', false, true, false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * @group delay queue
     */
    public function testDelayPub()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();

        $args = new AMQPTable();
        $args->set('x-dead-letter-exchange', self::$EX_ORDER);
        $args->set('x-dead-letter-routing-key', self::$DELAY_ROUTING_KEY);
        $channel->queue_declare(self::$DELAY_QUEUE, false,
            true, //开启队列持久化
            false, //true表示与消费者断连的时候删除队列
            false,false,
            $args);

        $msg = new AMQPMessage(StringUtils::randStr(mt_rand(3,30)),
            [
                //'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT, //开启消息持久化
                'expiration' => '30000'
            ]
        );
        $channel->basic_publish($msg, '', self::$DELAY_QUEUE);
        echo " [x] Sent ". $msg->body ."\n";
        $channel->close();
        $connection->close();
    }

    /**
     * @group delay queue
     */
    public function testDelayWorker()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $channel->exchange_declare(self::$EX_ORDER,'direct');
        $channel->queue_declare(self::$ORDER_QUEUE);
        $channel->queue_bind(self::$ORDER_QUEUE, self::$EX_ORDER, self::$DELAY_ROUTING_KEY);
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            echo " [x] Ack ",$msg->body, "\n";
        };
        $channel->basic_qos(null,1,null);
        $channel->basic_consume(
            self::$ORDER_QUEUE,
            '',
            false,
            false, //响应ACK,防止消息丢失
            false, false, $callback);
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }

    /**
     * @group  ack
     */
    public function testPublisherConfirm()
    {
        $connection = new AMQPStreamConnection($GLOBALS['HOST_GEEKIO'], 5672, 'rabbit', 'rabbit');
        $channel = $connection->channel();
        $msg_count = 10;
        $ack_count = 0;
        $channel->set_ack_handler(function (AMQPMessage $message) use(&$ack_count) {
            echo "[*] Ack msg:" . $message->body . PHP_EOL;
            $ack_count++;
        });
        $channel->set_nack_handler(function (AMQPMessage $message) {
            echo "[*] NAck msg:" . $message->body . PHP_EOL;
        });
        $channel->confirm_select();
        $routing_key = 'publisher_confirm';
        $channel->queue_declare($routing_key, false, false, false, false);

        $i = 1;
        while ($i <= $msg_count) {
            $msg = new AMQPMessage("Hello " . $i++);
            $channel->basic_publish($msg, '', $routing_key);
        }
        echo " [x] Sent all messages\n";
        $channel->wait_for_pending_acks();

        //驗證所有消息都發送成功
        $this->assertEquals($msg_count, $ack_count);

        $channel->close();
        $connection->close();
    }

}