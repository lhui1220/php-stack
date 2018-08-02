<?php

namespace PHPStack\Mongo;

use MongoDB\Client as MongoClient;
use PHPUnit\Framework\TestCase;

class MongoTest extends TestCase
{

    /**
     * @var MongoClient
     */
    private $mongo;

    public function setUp()
    {
        $user = 'lms_wr';
        $password = 'lms';
        $host = '192.168.6.67';
        $port = '27017';
        $db = 'lms';
        $uri = "mongodb://$user:$password@$host:$port/$db";
        $this->mongo = new MongoClient($uri);
    }

    public function testInsertMany()
    {
$json = <<<'EOD'
[
   { "item": "journal", "qty": 25, "status": "A",
       "size": { "h": 14, "w": 21, "uom": "cm" }, "tags": [ "blank", "red" ] },
   { "item": "notebook", "qty": 50, "status": "A",
       "size": { "h": 8.5, "w": 11, "uom": "in" }, "tags": [ "red", "blank" ] },
   { "item": "paper", "qty": 100, "status": "D",
       "size": { "h": 8.5, "w": 11, "uom": "in" }, "tags": [ "red", "blank", "plain" ] },
   { "item": "planner", "qty": 75, "status": "D",
       "size": { "h": 22.85, "w": 30, "uom": "cm" }, "tags": [ "blank", "red" ] },
   { "item": "postcard", "qty": 45, "status": "A",
       "size": { "h": 10, "w": 15.25, "uom": "cm" }, "tags": [ "blue" ] }
]
EOD;

        $inventory = json_decode($json, true);

        $result = $this->mongo->lms->inventory->insertMany($inventory);

        $this->assertNotNull($result);
        $this->assertNotEmpty($result->getInsertedIds());
        $this->assertEquals($result->getInsertedCount(), 5);
    }

    public function testDrop()
    {
        $this->mongo->lms->inventory->drop();
    }

    public function testFindOne()
    {
        $inventory = $this->mongo->lms->inventory->find([]);
        $this->assertNotNull($inventory);

        foreach ($inventory as $doc) {
            var_dump($doc['_id']);
            return ;
        }
    }


}