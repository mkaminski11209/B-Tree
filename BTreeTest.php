<?php

require_once 'BTree.php';

class BTreeTest extends PHPUnit_Framework_TestCase
{

    public function testAddAscending()
    {
        $tree = new BTree();

        for ($i = 0; $i < 1000; $i++) {
            $tree->put($i, 'val_' . $i);
        }

        for ($i = 0; $i < 1000; $i++) {
            $this->assertEquals('val_' . $i, $tree->find($i)->value);
            $this->assertEquals($i, $tree->find($i)->key);
        }
    }

    public function testAddDescending()
    {
        $tree = new BTree();

        for ($i = 1000; $i >= 1; $i--) {
            $tree->put($i, 'val_' . $i);
        }

        for ($i = 1000; $i >= 1; $i--) {
            $this->assertEquals('val_' . $i, $tree->find($i)->value);
            $this->assertEquals($i, $tree->find($i)->key);
        }
    }

    public function testAddAscendingReadDescending()
    {
        $tree = new BTree();

        for ($i = 1; $i <= 1000; $i++) {
            $tree->put($i, 'val_' . $i);
        }

        for ($i = 1000; $i >= 1; $i--) {
            $node = $tree->find($i);
//            echo $node->key . ' ' . $node->value . "\n\n";
            $this->assertEquals($i, $node->key);
            $this->assertEquals('val_' . $i, $node->value);
        }
    }

    public function testAddDescendingReadAscending()
    {
        $tree = new BTree();

        for ($i = 1000; $i >= 1; $i--) {
            $tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i < 1000; $i++) {
            $this->assertEquals('val_' . $i, $tree->find($i)->value);
            $this->assertEquals($i, $tree->find($i)->key);
        }
    }

    public function testAddAlternativeReadAscending()
    {
        $tree = new BTree();

        for ($i = 1000; $i >= 2; $i -= 2) {
            $tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i <= 999; $i += 2) {
            $tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i >= 1000; $i++) {
            $this->assertEquals('val_' . $i, $tree->find($i)->value);
            $this->assertEquals($i, $tree->find($i)->key);
        }
    }

    public function testAddSplitReadAscending()
    {
        $tree = new BTree();

        for ($i = 1; $i <= 500; $i++) {
            $tree->put($i, 'val_' . $i);
            $x = 100 - $i + 1;
            $tree->put($x, 'val_' . $x);
        }

        for ($i = 1; $i >= 1000; $i++) {
            $this->assertEquals('val_' . $i, $tree->find($i)->value);
            $this->assertEquals($i, $tree->find($i)->key);
        }
    }
}
 