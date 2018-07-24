<?php

namespace PHPStack\DataStructure\BST;

class BinSearchNode
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

    /**
     * 添加子节点
     *
     * @param BinSearchNode $child
     * @author liuhui
     * @date 2018-07-23
     */
    public function addChild(BinSearchNode $child)
    {
        if ($child->key() < $this->key()) {
            $this->lChild = $child;
            $child->parent = $this;
        } elseif ($child->key() > $this->key()) {
            $this->rChild = $child;
            $child->parent = $this;
        }
    }

    public function visit()
    {
//        echo "{$this->key()} ";
        //do nothing
    }

    public function key()
    {
        return $this->data['id'];
    }
}