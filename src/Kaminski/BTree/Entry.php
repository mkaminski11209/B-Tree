<?php
/**
 * Key/Value Store
 *
 * @author Mike Kaminski <michael.w.kaminski@gmail.com>
 * @since 5/7/2014
 */
namespace Kaminski\BTree;

class Entry
{
    /**
     * @param int $key
     * @param string $value
     */
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