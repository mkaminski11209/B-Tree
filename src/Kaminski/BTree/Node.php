<?php

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

    public $leaf = true;
}