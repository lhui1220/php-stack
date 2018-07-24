<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/5/14
 * Time: 14:21
 */

namespace PHPStack\DataStructure;

/**
 * 二叉树节点
 *
 * @author liuhui
 * @date ${DATE}
 */
class BinNode
{
    //数据域
    public $data;
    //左子树
    public $lChild = null;
    //右子树
    public $rChild = null;

    //父节点
    public $parent = null;
    public $height = -1;

    function __construct($data)
    {
        $this->data = $data;
        $this->height = 0;
    }

    public function updateHeight()
    {
        
    }

    public function visit()
    {
        echo "$this->data \n";
    }

}