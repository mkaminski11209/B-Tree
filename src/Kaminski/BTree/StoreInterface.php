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
     * @param Node $node
     * @return void
     */
    public function allocateNode(Node $node);

    /**
     * @param Node $node
     * @return void
     */
    public function writeNode(Node $node);

    /**
     * @param Node $parent_node
     * @param int $index
     * @param Node $child_node
     * @return void
     */
    public function writeChildNode(Node $parent_node, $index, Node $child_node);

    /**
     * @param Node $node
     * @return void
     */
    public function writeRootNode(Node $node);

    /**
     * @param Node $node
     * @param $min_key
     * @param $max_key
     * @return array
     */
    public function traverse(Node $node, $min_key, $max_key);
}