<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kaminski\BTree\BTree;

class BTreeTestFileStore extends PHPUnit_Framework_TestCase
{

    /**
     * @var BTree
     */
    private $tree;

    /**
     * @var FileStore
     */
    private $store;

    const FILE_STORE = '/tmp/db.txt';

    public function setUp()
    {
        touch(self::FILE_STORE);
        $this->store = new \Kaminski\BTree\FileStore(self::FILE_STORE, 3);
        $this->tree = new BTree($this->store);
    }

    public function tearDown()
    {
        unset($this->store);
        unlink(self::FILE_STORE);
    }

    public function testAddAscending()
    {
        for ($i = 0; $i < 500; $i++) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 0; $i < 500; $i++) {
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
        for ($i = 1; $i <= 500; $i++) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 500; $i >= 1; $i--) {
            $node = $this->tree->find($i);
//            echo $node->key . ' ' . $node->value . "\n\n";
            $this->assertEquals($i, $node->key);
            $this->assertEquals('val_' . $i, $node->value);
        }
    }

    public function testAddDescendingReadAscending()
    {
        for ($i = 500; $i >= 1; $i--) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i < 500; $i++) {
            $this->assertEquals('val_' . $i, $this->tree->find($i)->value);
            $this->assertEquals($i, $this->tree->find($i)->key);
        }
    }

    public function testAddAlternativeReadAscending()
    {
        for ($i = 500; $i >= 2; $i -= 2) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i <= 999; $i += 2) {
            $this->tree->put($i, 'val_' . $i);
        }

        for ($i = 1; $i >= 500; $i++) {
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

        for ($i = 1; $i >= 500; $i++) {
            $this->assertEquals('val_' . $i, $this->tree->find($i)->value);
            $this->assertEquals($i, $this->tree->find($i)->key);
        }
    }

    public function testUpdateValue()
    {
        $this->tree->put(10, 'old_val_10');
        $this->tree->put(10, 'new_val_10');
        $this->assertEquals('new_val_10', $this->tree->find(10)->value);
    }

    public function testEmptyResultWithEmptyTree()
    {
        $this->assertNull($this->tree->find(0));
    }

    public function testEmptyResultWithNonEmptyTree() {
        $this->tree->put(1, "someval");
        $this->assertNull($this->tree->find(10));
    }

    public function testFindRangeEmpty()
    {
        $this->assertEmpty($this->tree->getKeyRange(2, 10));
    }

    public function testFindRangeNonEmptyTreeWithMissingKeys()
    {
        $this->tree->put(1, "someval");
        $this->assertEmpty($this->tree->getKeyRange(2, 10));
    }

    public function testFindRangeNoneEmptyTree()
    {
        for ($i = 1; $i <= 100; $i++) {
            $this->tree->put($i, 'val_' . $i);
        }

        $range = $this->tree->getKeyRange(40, 70);

        $expected = range(40, 70);

        for ($i = 0; $i < count($expected); $i++) {
            $elem = $range[$i];
            $this->assertEquals($expected[$i], $range[$i]->key);
            $this->assertEquals('val_' . $expected[$i], $range[$i]->value);
        }
    }
}
 