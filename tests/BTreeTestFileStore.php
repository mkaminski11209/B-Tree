<?php

require_once __DIR__.'/../vendor/autoload.php';

use Kaminski\BTree\BTree;

class BTreeTestFileStore extends PHPUnit_Framework_TestCase
{

    /**
     * @var BTree
     */
    private $tree;

    public function setUp() {
        $store = new \Kaminski\BTree\FileStore('/tmp/db.txt', 3, true);
        $this->tree = new BTree($store);
    }

    public function testAddAscending()
    {
        for ($i = 0; $i < 1000; $i++) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 0; $i < 1000; $i++) {
            $this->assertEquals('val_' . $i, $this->tree->find($i)->value);
            $this->assertEquals($i, $this->tree->find($i)->key);
        }
    }

    public function testAddDescending()
    {
        for ($i = 10; $i >= 1; $i--) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 10; $i >= 1; $i--) {
            $this->assertEquals('val_' . $i, $this->tree->find($i)->value);
            $this->assertEquals($i, $this->tree->find($i)->key);
        }
    }

    public function testAddAscendingReadDescending()
    {
        for ($i = 1; $i <= 1000; $i++) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 1000; $i >= 1; $i--) {
            $node = $this->tree->find($i);
//            echo $node->key . ' ' . $node->value . "\n\n";
            $this->assertEquals($i, $node->key);
            $this->assertEquals('val_' . $i, $node->value);
        }
    }

    public function testAddDescendingReadAscending()
    {
        for ($i = 1000; $i >= 1; $i--) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i < 1000; $i++) {
            $this->assertEquals('val_' . $i, $this->tree->find($i)->value);
            $this->assertEquals($i, $this->tree->find($i)->key);
        }
    }

    public function testAddAlternativeReadAscending()
    {
        for ($i = 1000; $i >= 2; $i -= 2) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i <= 999; $i += 2) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i >= 1000; $i++) {
            $this->assertEquals('val_' . $i, $this->tree->find($i)->value);
            $this->assertEquals($i, $this->tree->find($i)->key);
        }
    }

    public function testAddSplitReadAscending()
    {
        for ($i = 1; $i <= 500; $i++) {
            $this->tree->put($i, 'val_' . $i);
            $x = 100 - $i + 1;
            $this->tree->put($x, 'val_' . $x);
        }

        for ($i = 1; $i >= 1000; $i++) {
            $this->assertEquals('val_' . $i, $this->tree->find($i)->value);
            $this->assertEquals($i, $this->tree->find($i)->key);
        }
    }
}
 