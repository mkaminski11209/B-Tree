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

    /**
     * @return string
     */
    public function __toString() {
        return "Key: ".$this->key." Value: ".$this->value;
    }
}