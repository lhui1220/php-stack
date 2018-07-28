<?php

namespace PHPStack\DataStructure\BST;

use PHPUnit\Framework\TestCase;

class BinSearchTreeTest extends TestCase
{
    /**
     * @var BinSearchTree
     */
    protected $tree;

    protected function setUp()
    {
        parent::setUp();

        $this->tree = new BinSearchTree();
        $this->tree->addNode(['id' => 7]);
        $this->tree->addNode(['id' => 6]);
        $this->tree->addNode(['id' => 3]);
        $this->tree->addNode(['id' => 2]);
        $this->tree->addNode(['id' => 4]);
        $this->tree->addNode(['id' => 8]);
        $this->tree->addNode(['id' => 1]);
        $this->tree->addNode(['id' => 5]);
    }

    public function testInOrder()
    {
        $result = $this->tree->inOrderWithIterate();
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $result);
    }

    public function testPreOrder()
    {
        $result = $this->tree->preOrderWithIterate();
        $this->assertEquals([7, 6, 3, 2, 1, 4, 5, 8], $result);
    }

    public function testSearchNode()
    {
        $node = $this->tree->searchNode(['id' => 1]);
        $this->assertNotNull($node);
        $this->assertEquals(1, $node->key());
    }

    public function testDeleteNode()
    {
        $this->tree->deleteNode(['id' => 5]);
        $result = $this->tree->inOrderWithIterate();
        $this->assertEquals([1, 2, 3, 4, 6, 7, 8], $result);

        $node = $this->tree->searchNode(['id' => 5]);
        $this->assertNull($node);
    }

    public function testDeleteRoot()
    {
        $this->tree->deleteNode(['id' => 7]);
        $this->assertEquals(true, $this->tree->isEmpty());
    }


    public function testDeleteHasLRChildNode()
    {
        $this->tree->deleteNode(['id' => 3]);
        $result = $this->tree->inOrderWithIterate();
        $this->assertEquals([1, 2, 4, 5, 6, 7, 8], $result);

        $node = $this->tree->searchNode(['id' => 3]);
        $this->assertNull($node);

        $successor = $this->tree->searchNode(['id' => 4]);

        $this->assertEquals($successor->parent->key(), 6);
        $this->assertEquals($successor->lChild->key(), 2);
        $this->assertEquals($successor->rChild->key(), 5);

    }

}