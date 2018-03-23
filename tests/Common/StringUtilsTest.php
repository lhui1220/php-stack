<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 11:48
 */

namespace PHPStack\Common;

use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{

    public function testRandStr()
    {
        $str = StringUtils::randStr(6);
        $this->assertNotEmpty($str, 'random str is empty');
        $this->assertEquals(6,strlen($str),'random str length not eq');
    }

}