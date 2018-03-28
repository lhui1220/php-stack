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

    public function testRedisSlot()
    {
        print_r($this->crc16("foo")."\n");
//        $this->assertEquals(12182,$this->crc16("foo") % 16384);
        $this->assertEquals(866,13558 % 16384);
    }

    function crc16($data)
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++)
        {
            $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        return $crc;
    }

}