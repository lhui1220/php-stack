<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/5/14
 * Time: 14:24
 */

namespace PHPStack\DataStructure;

class BinTree
{
    private $root;

    function __construct($root)
    {
        $this->root = $root;
    }

    public function insertAsLeft($node,$newNode)
    {
        $node->lChild = $newNode;
    }

    public function insertAsRight($node,$newNode)
    {
        $node->rChild = $newNode;
    }

    public function preOrder($node)
    {
        if (!$node) return ;
        $node->visit();
        $this->preOrder($node->lChild);
        $this->preOrder($node->rChild);
    }

    public function inOrder($node)
    {
        if (!$node) return ;
        $this->inOrder($node->lChild);
        $node->visit();
        $this->inOrder($node->rChild);
    }

    public function postOrder($node)
    {
        if (!$node) return ;
        $this->postOrder($node->lChild);
        $this->postOrder($node->rChild);
        $node->visit();
    }

    /**
     * @return mixed
     */
    public function getRoot()
    {
        return $this->root;
    }

}