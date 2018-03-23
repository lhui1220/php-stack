<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 13:37
 */

namespace PHPStack\Common;


use PHPUnit\Framework\TestCase;

class StackTest extends TestCase
{

    public function testPush()
    {
        $stack = [];
        array_push($stack, 'first');
        array_push($stack, 'second');

        $this->assertEquals(2, count($stack));

        return $stack;
    }

    /**
     *
     * @depends testPush
     *
     * @param $stack
     */
    public function testPop($stack)
    {
        $this->assertNotEmpty($stack);
        $e = array_pop($stack);
        $this->assertEquals('second',$e);
        $this->assertEquals(1, count($stack));
        return $stack;
    }

    /**
     * @depends testPop
     *
     * @param $stack
     */
    public function testEmpty($stack)
    {
        $this->assertNotEmpty($stack);
        $e = array_pop($stack);
        $this->assertEquals('first',$e);
        $this->assertEmpty($stack);
    }

}