<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 10:40
 */

namespace PHPStack\Redis;

use PHPStack\Common\StringUtils;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Predis\Response\Status;

class RedisClusterTest extends TestCase
{
    private $cluster;

    private $redis1;

    private $redis2;

    public function setUp()
    {

    }

    protected function tearDown()
    {
        $this->cluster = null;
        $this->redis1 = null;
        $this->redis2 = null;
    }

    public function testClientSharding()
    {

        $options = [
            ['scheme' => 'tcp', 'host' => '193.112.57.240', 'port' => 6379],
            ['scheme' => 'tcp', 'host' => '193.112.57.240', 'port' => 6380]
        ];
        $this->cluster = new Client($options);

        $this->redis1 = new Client(['host' => '193.112.57.240', 'port' => 6379]);
        $this->redis2 = new Client(['host' => '193.112.57.240', 'port' => 6380]);

        $this->redis1->flushdb();
        $this->redis2->flushdb();

        for ($i = 0; $i < 100; $i++) {
            $this->cluster->set("key:$i", StringUtils::randStr(32));
        }

        $res1 = $this->redis1->scan(0);
        $res2 = $this->redis2->scan(0);

        $this->assertNotEmpty($res1);
        $this->assertNotEmpty($res1[1]);

        $this->assertNotEmpty($res2);
        $this->assertNotEmpty($res2[1]);

        print_r("res1=".count($res1[1])." res2=".count($res1[2]));

        $this->redis1->flushdb();
        $this->redis2->flushdb();
    }

    public function testReplication()
    {
        $master = new Client(['host' => '193.112.57.240', 'port' => 6379]);

        $slave1 = new Client(['host' => '193.112.57.240', 'port' => 6380]);
        $slave2 = new Client(['host' => '193.112.57.240', 'port' => 6381]);

        $master->flushdb();

        $master->set('key','val');

        $this->assertEquals('val',$master->get('key'));
        $this->assertEquals('val',$slave1->get('key'));
        $this->assertEquals('val',$slave2->get('key'));

        $master->flushdb();
    }

    public function testCluster()
    {
        $nodes = [
            ['host' => '193.112.57.240', 'port' => 30001],
            ['host' => '193.112.57.240', 'port' => 30002],
            ['host' => '193.112.57.240', 'port' => 30003]
        ];
        $options = ['cluster'=>'redis','profile'=>'3.2'];

        $cluster = new Client($nodes, $options);
        $res = $cluster->set('foo','bar');

        $this->assertEquals(Status::get('OK'),$res);
        $this->assertEquals('bar',$cluster->get('foo'));

    }
}