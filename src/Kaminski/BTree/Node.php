<?php
/**
 * B-Tree Node
 *
 * @author Mike Kaminski <michael.w.kaminski@gmail.com>
 * @since 5/6/2014
 */
namespace Kaminski\BTree;

class Node
{
    /**
     * @var Entry[]
     */
    public $keys = array();

    /**
     * @var Node[]
     */
    public $children;

    /**
     * @var int
     */
    public $offset;
}