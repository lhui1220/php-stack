<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/5/14
 * Time: 14:16
 */

namespace PHPStack\DataStructure;

class Stack
{
    private $array = [];

    public function push($e)
    {
        return array_push($this->array,$e);
    }

    public function pop()
    {
        return array_pop($this->array);
    }

    public function peek()
    {
        if ($this->isEmpty()) return null;
        $idx = count($this->array) - 1;
        return $this->array[$idx];
    }

    public function isEmpty()
    {
        return empty($this->array);
    }

}