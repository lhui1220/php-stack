<?php
/**
 * Created by PhpStorm.
 * User: liuhui1
 * Date: 2018/5/14
 * Time: 14:30
 */

namespace PHPStack\DataStructure;

use PHPUnit\Framework\TestCase;

class BinTreeTest extends TestCase
{

    private $tree;

    protected function setUp()
    {
        parent::setUp();
        $this->initTree();
    }

    public function testPreOrder()
    {
        $this->tree->preOrder($this->tree->getRoot());
    }

    public function testInOrder()
    {
        $this->tree->inOrder($this->tree->getRoot());
    }

    public function testPostOrder()
    {
        $this->tree->postOrder($this->tree->getRoot());
    }

    /**
     *              a
     *            /  \
     *           b    c
     *         /  \
     *        d    e
     * @author liuhui
     * @date 2018-05-14
     */
    private function initTree()
    {
        $root = new BinNode('a');
        $b = new BinNode('b');
        $c = new BinNode('c');
        $d = new BinNode('d');
        $e = new BinNode('e');
        $tree = new BinTree($root);
        $tree->insertAsLeft($root,$b);
        $tree->insertAsRight($root,$c);
        $tree->insertAsLeft($b,$d);
        $tree->insertAsRight($b,$e);

        $this->tree = $tree;
    }

}