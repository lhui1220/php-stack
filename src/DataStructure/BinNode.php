<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/5/14
 * Time: 14:21
 */

namespace PHPStack\DataStructure;

class BinNode
{
    public $data;
    public $lChild = null;
    public $rChild = null;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function visit()
    {
        echo "$this->data \n";
    }

}