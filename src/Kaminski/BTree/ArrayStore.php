<?php

namespace Kaminski\BTree;

class ArrayStore implements StoreInterface
{
    /**
     * @var Node
     */
    private $rootNode;

    public function __construct()
    {
        $this->rootNode = new Node();
    }

    /**
     * @return Node
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * @param Node $node
     * @param int $index
     * @return Node|mixed
     */
    public function getChildNode(Node $node, $index)
    {
        return $node->children[$index];
    }

    /**
     * @param Node $parent_node
     * @param int $index
     * @param Node $child_node
     * @return mixed|void
     */
    public function writeChildNode(Node $parent_node, $index, Node $child_node)
    {
        $array_size = count($parent_node->children);
        for ($j = $array_size; $j > $index; $j--) {
            $parent_node->children[$j] = $parent_node->children[$j - 1];
        }
        $parent_node->children[$j] = $child_node;
    }

    public function writeRootNode(Node $node)
    {
        $this->rootNode = $node;
    }
}