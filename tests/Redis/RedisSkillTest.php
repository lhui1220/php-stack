<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 10:40
 */

namespace PHPStack\Redis;

use PHPUnit\Framework\TestCase;

class RedisSkillTest extends TestCase
{
    private $redis;

    public function setUp()
    {
        $this->redis = new RedisSkill();
    }

    public function testSaveTTLKey()
    {
        $val = $this->redis->saveTTLKey();
        $this->assertEquals("1",$val,"fail");
    }
}