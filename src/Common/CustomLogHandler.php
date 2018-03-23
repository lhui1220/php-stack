<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 15:54
 */

namespace PHPStack\Common;


use Elastica\Client;
use Monolog\Handler\ElasticSearchHandler;
use Monolog\Logger;

class CustomLogHandler extends ElasticSearchHandler
{

    public function __construct(Client $client, array $options = array(), $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($client, $options, $level, $bubble);
    }

    public function handleBatch(array $records)
    {
//        parent::handleBatch($records);

        var_dump($records);

        echo "handle batch called.<br/>";
    }
}