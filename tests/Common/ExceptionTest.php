<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/3/17
 * Time: 13:51
 */

namespace PHPStack\Common;

use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testPHPError()
    {
        include 'not_existing_file.php';
    }

    /**
     * @dataProvider argProvider
     *
     * @expectedException \InvalidArgumentException
     */
    public function testException($a, $b)
    {
        if ($a + $b < 3) {
            throw new \InvalidArgumentException("invalid argument.");
        }
        return $a + $b;
    }

    public function argProvider()
    {
        return [[1,1]];
    }
}