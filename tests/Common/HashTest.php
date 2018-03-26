<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/26
 * Time: 9:09
 */

namespace PHPStack\Common;

use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    private $hashRing;

    public function setUp()
    {
        $this->hashRing = new HashRing();
    }

    public function testConsistentHash()
    {
        $key = 'counter';
        $this->hashRing->set($key, 1);
        $this->hashRing->set($key, 2);
        $this->hashRing->set($key, 3);

        $this->assertEquals(3,$this->hashRing->get($key));
    }

}