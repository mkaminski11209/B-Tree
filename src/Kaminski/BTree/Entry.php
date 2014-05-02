<?php

namespace Kaminski\BTree;

class Entry
{
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $value;
}