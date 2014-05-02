<?php

namespace Kaminski\BTree;

interface StoreInterface
{

    /**
     * @return mixed
     */
    public function getRootNode();

    /**
     * @param Node $node
     * @param int $index
     * @return mixed
     */
    public function getChildNode(Node $node, $index);

    /**
     * @param Node $parent_node
     * @param int $index
     * @param Node $child_node
     * @return mixed
     */
    public function writeChildNode(Node $parent_node, $index, Node $child_node);

    /**
     * @param Node $node
     * @return mixed
     */
    public function writeRootNode(Node $node);
}