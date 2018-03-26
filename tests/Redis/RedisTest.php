<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/22
 * Time: 18:41
 */

namespace PHPStack\Redis;


use PHPStack\Common\StringUtils;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Predis\Response\Status;

class RedisTest extends TestCase
{
    private $redis;

    public function setUp()
    {
        $this->redis = $this->createConnection();
    }

    public function tearDown()
    {
        $this->redis = null;
    }

    /**
     * @group tx
     */
    public function testTransaction()
    {

        //清理数据库
        $this->redis->flushdb();

        //初始化数据
        $accountA = 'account:1';
        $accountB = 'account:2';

        $this->redis->hmset('account:1',['id'=>1,'account'=>'A','balance'=>200]);
        $this->redis->hmset('account:2',['id'=>2,'account'=>'B','balance'=>100]);

        //运行测试
        //start tx
        $this->redis->multi();
        // queued commands
        $this->redis->hincrby($accountA,'balance',-100);
        $this->redis->hincrby($accountB,'balance',100);
        //exec tx
        $this->redis->exec();

        //验证测试
        $this->assertEquals(100,$this->redis->hget($accountA,'balance'));
        $this->assertEquals(200,$this->redis->hget($accountB,'balance'));

        //清理数据库
        $this->redis->flushdb();
    }

    /**
     * @group tx
     */
    public function testDiscardTransaction()
    {

        /**清理数据库*/
        $this->redis->flushdb();

        /**初始化数据*/
        $accountA = 'account:1';
        $accountB = 'account:2';

        $this->redis->hmset('account:1',['id'=>1,'account'=>'A','balance'=>200]);
        $this->redis->hmset('account:2',['id'=>2,'account'=>'B','balance'=>100]);

        /**运行测试*/

        //开启事务
        $this->redis->multi();
        //命令入队
        $this->redis->hincrby($accountA,'balance',-100);
        $this->redis->hincrby($accountB,'balance',100);
        //取消执行
        $this->redis->discard();

        /**验证测试*/
        $this->assertEquals(200,$this->redis->hget($accountA,'balance'));
        $this->assertEquals(100,$this->redis->hget($accountB,'balance'));

        /**清理数据库*/
        $this->redis->flushdb();

    }

    /**
     * @group tx
     */
    public function testFailTransaction()
    {

        /**清理数据库*/
        $this->redis->flushdb();

        /**初始化数据*/
        $accountA = 'account:1';
        $accountB = 'account:2';

        $this->redis->hmset('account:1',['id'=>1,'account'=>'A','balance'=>200]);
        $this->redis->hmset('account:2',['id'=>2,'account'=>'B','balance'=>100]);

        /**运行测试*/

        //开启事务
        $this->redis->multi();
        //命令入队
        $this->redis->hincrby($accountA,'balance',-100);
        $res = $this->redis->hincrby($accountB,'balance','a'); //case fail
        print_r($res);
        $this->redis->hincrby($accountB,'balance',100);
        //执行事务
        $res = $this->redis->exec();
        print_r($res);

        /**验证测试*/
        $this->assertEquals(100,$this->redis->hget($accountA,'balance'));
        $this->assertEquals(200,$this->redis->hget($accountB,'balance'));

        /**清理数据库*/
        $this->redis->flushdb();

    }


    /**
     * @group performance
     */
    public function testBatchSave()
    {
        $this->redis->flushdb();

        $pipe = $this->redis->pipeline();
        $size = 0; //待执行的命令数
        for ($i = 0; $i < 1024; $i++) {
            $pipe->set("key:$i",StringUtils::randStr(32));
            $size++;
            if ($i != 0 && $i % 100 == 0) {
                $res = $pipe->execute();
//                print_r($res);
                $size = 0;
            }
        }
        if ($size > 0) {
            $pipe->execute();
        }

        $cursor = 0;
        $count = 0;

        do {
            $res = $this->redis->scan($cursor,['count'=>100, 'match'=>'key:*']);
            $cursor = $res[0];
            $count += count($res[1]);
        } while ($cursor > 0);

        $this->assertEquals(1024, $count);

        $this->redis->flushdb();

    }

    /**
     * @group performance
     */
    public function testPipelineBench()
    {
        $withTime = $this->withPipeline();
        $withoutTime = $this->withoutPipeline();

        echo "withoutPipeline cost $withoutTime ms \n";
        echo "withPipeline cost $withTime ms \n";
    }

    /**
     * @group scan
     */
    public function testScan()
    {
        $this->redis->flushdb();
        $this->redis->set('key:str','sssssssssstr');
        $this->redis->hmset('key:hash',['field1'=>'hhhhhhhhhhhhash1','field2'=>'hhhhhhhash2']);

        $cursor = 0;
        $count = 0;

        do {
            $res = $this->redis->scan($cursor,['count'=>100, 'match'=>'key:*']);
            $cursor = $res[0];
            $count += count($res[1]);
        } while ($cursor > 0);

        $this->assertEquals(2, $count);

        $this->redis->flushdb();

    }

    /**
     * @group atomic
     */
    public function testRaceCondition()
    {

        /**清理数据库*/
        $this->redis->flushdb();

        /**初始化数据*/
        $key = 'counter';
        $this->redis->set($key,1);

        /**运行测试*/
        $this->redis->watch($key);
        $val = $this->redis->get($key); //读
        $val = $val + 1;    //改
        sleep(10); //10s内开启另一个客户端对该key进行+1操作
        //开启事务
        $this->redis->multi();
        //命令入队
        $this->redis->set($key,$val);//写
        //执行事务
        $res = $this->redis->exec();

        $val = $this->redis->get($key);

        /**验证测试*/
        $this->assertNull($res);
        $this->assertEquals(2,$val);

        /**清理数据库*/
        $this->redis->flushdb();
    }

    /**
     * @group atomic
     */
    public function testAtomicIncr()
    {
        /**清理数据库*/
        $this->redis->flushdb();

        /**初始化数据*/
        $key = 'counter';
        $this->redis->set($key,1);

        /**运行测试*/
        $this->atomicIncr($key);

        $val = $this->redis->get($key);

        /**验证测试*/
        $this->assertEquals(3,$val);

        /**清理数据库*/
        $this->redis->flushdb();
    }

    /**
     * @group lua
     */
    public function testLuaScript()
    {
        $master = new Client(['host' => '193.112.57.240', 'port' => 6379]);

        $master->flushdb();

        $lua = <<<EOD
            local dict = {num = 28, str='foobar'}
            for k,v in pairs(dict) do
                print(k..'='..v)
            end

            return redis.call('hmset',KEYS[1],ARGV[1],ARGV[2],ARGV[3],ARGV[4])
EOD;

        $res = $master->eval($lua,1,'c:1','total',100,'remain',98);

//        $lua_sha1 = sha1($lua);
//        $res = $master->evalsha($lua_sha1,1,'c:1','total',100,'remain',98);

//        $res = $master->executeRaw(['EVALSHA',$lua_sha1,1,'c:1','total',100,'remain',98]);

        $this->assertEquals(Status::get('OK'),$res);

        $this->assertEquals(100,$master->hget('c:1','total'));
        $this->assertEquals(98,$master->hget('c:1','remain'));

        list($total,$remain) = $master->hmget('c:1',['total','remain']);
        $this->assertEquals(100, $total);
        $this->assertEquals(98, $remain);

        $master->flushdb();

    }

    /**
     * @group lua
     */
    public function testFlashSale()
    {
        $master = new Client(['host' => '193.112.57.240', 'port' => 6379]);

        $master->flushdb();

        $master->hmset('c:1',['total'=>100,'remain'=>98]);

        $lua = <<<EOD
        
            local key = KEYS[1]
            local qty = tonumber(ARGV[1])
        
            local stock = tonumber(redis.call('hget',key,'remain'))
            
            if  stock > 0 and stock >= qty then
                redis.call('hset',key,'remain',stock-qty)
                return 1
            end
            
            return 0
EOD;

        $res = $master->eval($lua,1,'c:1',98);
        $this->assertEquals(1,$res);
        list($total,$remain) = $master->hmget('c:1',['total','remain']);
        $this->assertEquals(100, $total);
        $this->assertEquals(0, $remain);

        //=======================================

        $res = $master->eval($lua,1,'c:1',1);
        $this->assertEquals(0,$res);
        list($total,$remain) = $master->hmget('c:1',['total','remain']);
        $this->assertEquals(100, $total);
        $this->assertEquals(0, $remain);

        $master->flushdb();
    }

    private function atomicIncr($key)
    {
        do {
            $this->redis->watch($key);
            $val = $this->redis->get($key);
            $val = $val + 1;
            sleep(10); //10s内开启另一个客户端对该key进行+1操作
            $this->redis->multi();
            $this->redis->set($key, $val);
            $res = $this->redis->exec();
        } while ($res === null);
    }

    private function withPipeline()
    {
        $starTime = floor(microtime(true) * 1000);
        $this->redis->pipeline(function ($pipe) {

            for ($i = 0; $i < 10000; $i++) {
                $pipe->ping();
            }

        });
        $endTime = floor(microtime(true) * 1000);

        return ($endTime - $starTime);
    }

    private function withoutPipeline()
    {
        $starTime = floor(microtime(true) * 1000);
        for ($i = 0; $i < 10000; $i++) {
            $this->redis->ping();
        }
        $endTime = floor(microtime(true) * 1000);

        return ($endTime - $starTime);
    }

    private function createConnection()
    {
        $options = [
            'scheme' => 'tcp',
            'host' => $GLOBALS['REDIS_HOST'],
            'port' => $GLOBALS['REDIS_PORT'],
            'db' => $GLOBALS['REDIS_DB']
        ];
        return new Client($options);
    }

}