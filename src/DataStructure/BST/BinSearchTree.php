<?php

namespace PHPStack\DataStructure\BST;

use PHPStack\DataStructure\Stack;

class BinSearchTree
{
    /**
     * @var BinSearchNode 根节点
     */
    private $root;

    /**
     * @var Stack 利用栈实现BST的遍历
     */
    private $stack;

    /**
     * @var array 遍历结果
     */
    private $results;

    function __construct()
    {
        $this->stack = new Stack();
    }

    /**
     * 添加节点
     *
     * @param $data
     * @author liuhui
     * @date 2018-07-23
     */
    public function addNode($data)
    {
        $newNode = new BinSearchNode($data);
        if ($this->root == null) {
            $this->root = $newNode;
            return ;
        }
        $parent = $this->searchPosition($data);

        $parent->addChild($newNode);
    }

    /**
     * 删除节点
     *
     * @param $data
     * @author liuhui
     * @date 2018-07-24
     */
    public function deleteNode($data)
    {
        $node = $this->searchNode($data);  //搜索待删除节点

        if (!$node) {
            throw new \InvalidArgumentException("node not found");
        }

        if ($node->key() == $this->root->key()) {
            $this->root = null; //删除整棵树
        }elseif (!$node->lChild && !$node->rChild) {
            //没有左子节点和右子节点
            if ($this->isLeftChild($node)) {
                $node->parent->lChild = null;
            }

            if ($this->isRightChild($node)) {
                $node->parent->rChild = null;
            }
        } elseif ($node->lChild && !$node->rChild) {
            //只有左子节点
            $node->parent->lChild = $node->lChild;
            $node->lChild->parent = $node->parent;
        } elseif ($node->rChild && !$node->lChild) {
            //只有右子节点
            $node->parent->rChild = $node->rChild;
            $node->rChild->parent = $node->parent;
        } else {
            //既有左子节点又有右子节点
            $successor = $this->findSuccessor($node);
            $this->swap($node, $successor);
            $successor->parent->rChild = $successor->rChild;

        }
    }

    /**
     * 交互两个节点的数据
     *
     * @param BinSearchNode $left
     * @param BinSearchNode $right
     * @author liuhui
     * @date 2018-07-25
     */
    protected function swap($left, $right)
    {
        $tmp = $left->data;
        $left->data = $right->data;
        $right->data = $tmp;
    }

    /**
     * 查找指定节点中序遍历的直接后继节点
     *
     * @param BinSearchNode $node
     * @return null|BinSearchNode
     *
     * @author liuhui
     * @date 2018-07-24
     */
    public function findSuccessor($node)
    {
        $prev = $node->rChild;
        $current = $node->rChild;
        while ($current) {
            $prev = $current;
            $current = $current->lChild;
        }
        return $prev;
    }

    public function isEmpty()
    {
        return isset($this->root) ? false : true;
    }

    /**
     * 使用迭代方式实现先序遍历
     * 时间复杂度: O(n)
     *
     * @author liuhui
     * @date 2018-07-24
     */
    public function preOrderWithIterate()
    {

        $this->resetResults();
        $current = $this->root; //从根节点开始遍历

        while (true) {
            $this->visitAlongLeftBranch($current);

            $node = $this->stack->pop();

            if (!$node) break; //所有节点遍历完毕

            $current = $node;
        }
        return $this->results;
    }

    /**
     * 使用迭代方式实现中序遍历
     * 时间复杂度: O(n)
     *
     * @author liuhui
     * @date 2018-07-23
     */
    public function inOrderWithIterate()
    {
        $this->resetResults();
        $current = $this->root;
        while (true) {
            $this->goAlongLeftBranch($current); //O(n)
            $node = $this->stack->pop();

            if (!$node) break;

            $this->visit($node); //O(n)

            $current = $node->rChild;
        }
        return $this->results;
    }

    /**
     * 使用迭代方式实现后序遍历
     * 时间复杂度: O(n)
     *
     * @author liuhui
     * @date 2018-07-24
     */
    public function postOrderWithIterate()
    {
        $this->resetResults();

    }


    /**
     * 搜索待插入的位置
     *
     * @param $data
     * @return BinSearchNode
     *
     * @author liuhui
     * @date 2018-07-23
     */
    public function searchPosition($data)
    {
        $key = $data['id'];
        $parent = $current = $this->root;
        while ($current) {
            $parent = $current;
            if ($key < $current->key()) {
                $current = $current->lChild;
            } elseif ($key > $current->key()) {
                $current = $current->rChild;
            }
        }
        return $parent;
    }

    /**
     * 搜索指定节点
     *
     * @param $data
     * @return null|BinSearchNode
     * @author liuhui
     * @date 2018-07-24
     */
    public function searchNode($data)
    {
        $searchKey = $data['id'];

        $current = $this->root; //从根节点开始遍历

        while ($current) {
            if ($current->key() == $searchKey) {
                return $current;
            }elseif ($current->key() > $searchKey) {
                $current = $current->lChild;
            }else {
                $current = $current->rChild;
            }
        }
        return null;    //没有找到指定节点
    }

    /**
     * 沿左侧链遍历
     *
     * @param BinSearchNode $node
     * @author liuhui
     * @date 2018-07-23
     */
    protected function goAlongLeftBranch($node)
    {
        $current = $node;
        while ($current) {
            $this->stack->push($current);
            $current = $current->lChild;
        }
    }

    /**
     * 沿左侧链访问
     *
     * @param BinSearchNode $node
     * @author liuhui
     * @date 2018-07-23
     */
    protected function visitAlongLeftBranch($node)
    {
        if (!$node) {
            return ;
        }
        $this->visit($node);

        if ($node->rChild) $this->stack->push($node->rChild); //右子节点先入栈

        if ($node->lChild) $this->stack->push($node->lChild); //左子节点先入栈
    }

    /**
     * 访问某个节点
     *
     * @param BinSearchNode $node
     * @author liuhui
     * @date 2018-06-00
     */
    protected function visit($node)
    {
        $node->visit();

        $this->results[] = $node->key();
    }

    protected function resetResults()
    {
        $this->results = [];
    }

    /**
     * 检测是否是左子节点
     *
     * @param BinSearchNode $node
     * @return bool
     *
     * @author liuhui
     * @date 2018-07-24
     */
    protected function isLeftChild($node)
    {
        if (!$node->parent) {
            return false;
        }

        $lChild = $node->parent->lChild;
        if ($lChild && $lChild->key() == $node->key()) {
            return true;
        }
        return false;
    }

    /**
     * 检测是否是右子节点
     *
     * @param BinSearchNode $node
     * @return bool
     *
     * @author liuhui
     * @date 2018-07-24
     */
    protected function isRightChild($node)
    {
        if (!$node->parent) {
            return false;
        }
        $rChild = $node->parent->rChild;
        if ($rChild && $rChild->key() == $node->key()) {
            return true;
        }
        return false;
    }

}