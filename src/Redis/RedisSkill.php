<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 10:09
 */

namespace PHPStack\Redis;

use PHPStack\Common\StringUtils;
use PHPStack\Framework\Container;
use Predis\Client;

class RedisSkill
{
    private $redis;

    public function __construct($options = array())
    {
        $options += ['scheme'=>'tcp', 'host'=>'193.112.57.240', 'port'=>6379];

        $this->redis = new Client($options);

//        Container::getInstance()->getLogger()->debug("redis skill contruct");
    }

    public function __destruct()
    {
        if (isset($this->redis)) {
            $this->redis->disconnect();
        }
//        Container::getInstance()->getLogger()->debug("redis skill destruct");
    }

    public function saveBigKey()
    {
       for ($i=1; $i<=256;$i++) {
            $this->redis->set('user:'.sprintf('%04d',$i),StringUtils::randStr(4087));
            sleep(1);
        }
    }

    public function saveBigHash()
    {

    }

    /**
     * 使用事务
     */
    public function startTx()
    {
        $accountA = 'account:1';
        $accountB = 'account:2';

        $this->redis->hmset('account:1',['id'=>1,'account'=>'A','balance'=>200]);
        $this->redis->hmset('account:2',['id'=>2,'account'=>'B','balance'=>100]);

        //start tx
        $this->redis->multi();
        // queued commands
        $this->redis->hincrby($accountA,'balance',100);
        $this->redis->hincrby($accountB,'balance',-100);
        //exec tx
//        $this->redis->exec();
        $this->redis->discard();


    }

    public function saveTTLKey()
    {
        Container::getInstance()->getLogger()->debug("save ttl key");
        $key = 'token:123456';
        $this->redis->setex($key, 60, "1");
        return $this->redis->get($key);
    }

    public function hop()
    {
        $data = ['name'=>'张三'];
        return json_encode($data, JSON_UNESCAPED_UNICODE);
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
        $cluster->set('foo','bar');

        echo $cluster->get('foo');

    }

}