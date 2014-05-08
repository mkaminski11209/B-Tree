<?php
/**
 * Abstraction for B-Tree store for the B-Tree class
 *
 * @author Mike Kaminski <michael.w.kaminski@gmail.com>
 * @since 5/6/2014
 */
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
     * @return int
     */
    public function getMaxKeys();
}