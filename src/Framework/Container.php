<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 17:04
 */

namespace PHPStack\Framework;

use Elastica\Client;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\ElasticSearchHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * The IoC Container
 *
 * @package PHPStack\Framework
 */
class Container
{
    private $logger;

    private static $instance;

    private function __construct()
    {
        $this->logger = new Logger("php-stack");
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/../../app.log',Logger::DEBUG));

        $esClient = new Client(['host'=>'193.112.57.240','port'=>9200]);
        $this->logger->pushHandler(new BufferHandler(new ElasticSearchHandler($esClient)));
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 私有化clone方法，防止clone出多个实例
     */
    private function __clone()
    {
    }


    public function getLogger() {
        return $this->logger;
    }

}