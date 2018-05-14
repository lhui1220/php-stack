<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/5/8
 * Time: 9:12
 */

namespace PHPStack\Common;


use PHPUnit\Framework\TestCase;

class RefTest extends TestCase
{

    public function testBasicRef()
    {
        $a = 'old string';
        $b = &$a;
        $b = 'new string';

        $this->assertEquals('new string',$a);
        $this->assertEquals('new string',$b);
    }

    public function testRefArg()
    {
        $var = 'old val';
        $ret = $this->changeRefArg($var);
        $this->assertEquals($ret, $var);
    }

    public function testRefRet()
    {
        $arr_ref = $this->returnRef();
        $arr = $this->returnNonRef();

        xdebug_debug_zval('arr_ref');
        xdebug_debug_zval('arr');
    }

    public function testArrPass()
    {
        $arr = ['a','b','c'];
        $this->changeArr($arr);
        $this->assertEquals(['a','b','c'],$arr);
        $this->changeArrRef($arr);
        $this->assertEquals(['1','b','c'],$arr);
    }

    public function testObjPass()
    {
        $obj = new \stdClass();
        $obj->filed = 'a';
        $this->changeObj($obj);

        $this->assertEquals('1',$obj->filed);
    }

    private function &returnRef()
    {
        $arr = ['a','b','c'];
        return $arr;
    }

    private function returnNonRef()
    {
        $arr = ['a','b','c'];
        return $arr;
    }

    private function changeRefArg(&$var)
    {
           $var = 'new val';
           return $var;
    }

    private function changeArr($arr)
    {
        $arr[0] = '1';
    }

    private function changeArrRef(&$arr)
    {
        $arr[0] = '1';
    }

    private function changeObj($obj)
    {
        $obj->filed = '1';
    }

}