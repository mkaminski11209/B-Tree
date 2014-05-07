<?php

namespace Kaminski\BTree;

use \OutOfRangeException;
use \RuntimeException;

class FileStore implements StoreInterface
{

    const NODE_SIZE_BYTES = 4096;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var resource
     */
    private $fileHandler;

    /**
     * @var Node
     */
    private $rootNode;

    /**
     * @var bool
     */
    private $overWrite;

    /**
     * @param string $file_name Full path to database file
     * @param bool $over_write Clear database file before writing
     */
    public function __construct($file_name, $over_write = false)
    {
        $this->fileName = $file_name;
        $this->overWrite = $over_write;
        $this->setFileHandler();
        $this->setRootNode();
    }

    public function __destruct()
    {
        if ($this->fileHandler) {
            fclose($this->fileHandler);
        }
    }

    /**
     * @return Node
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * @param Node $node
     * @param int $index
     * @throws \RuntimeException
     * @return Node
     */
    public function getChildNode(Node $node, $index)
    {
        $child_offset = $node->children[$index];
        fseek($this->fileHandler, $child_offset);
        $contents = fread($this->fileHandler, self::NODE_SIZE_BYTES);
        $contents = unserialize($contents);
        if (!$contents instanceof Node) {
            throw new RuntimeException("Unserialized object must be instance of Node");
        }
        return $contents;
    }

    /**
     * @param Node $parent_node
     * @param int $index
     * @param Node $child_node
     * @return void
     */
    public function writeChildNode(Node $parent_node, $index, Node $child_node)
    {
        $child_offset = $child_node->offset;

        $child_node->offset = $child_offset;
        fseek($this->fileHandler, $child_offset);
        fwrite($this->fileHandler, $this->serialize($child_node), self::NODE_SIZE_BYTES);
        $parent_node->children[$index] = $child_offset;
        $this->writeNode($child_node);
        $this->writeNode($parent_node);
    }

    /**
     * @param Node $node
     * @return void
     */
    public function allocateNode(Node $node)
    {
        fseek($this->fileHandler, -self::NODE_SIZE_BYTES, SEEK_END);
        $node->offset = ftell($this->fileHandler);
        fwrite($this->fileHandler, $this->serialize($node), self::NODE_SIZE_BYTES);

        $this->writeRootNode($this->getRootNode());
    }

    /**
     * @param Node $node
     * @throws \RuntimeException
     * @return void
     */
    public function writeNode(Node $node)
    {
        if (is_int($node->offset)) {
            fseek($this->fileHandler, $node->offset);
            fwrite($this->fileHandler, $this->serialize($node), self::NODE_SIZE_BYTES);
        } else {
            throw new RuntimeException('Requires offset');
        }
    }

    /**
     * @param Node $node
     * @return void
     */
    public function writeRootNode(Node $node)
    {
        fseek($this->fileHandler, 0, SEEK_END);
        fwrite($this->fileHandler, $this->serialize($node), self::NODE_SIZE_BYTES);

        $node->offset = ftell($this->fileHandler) - self::NODE_SIZE_BYTES;
        $this->rootNode = $node;
    }

    /**
     * @param Node $node
     * @param $min_key
     * @param $max_key
     * @throws \OutOfRangeException
     * @return array
     */
    public function traverse(Node $node, $min_key, $max_key)
    {
        if ($min_key > $max_key) {
            throw new OutOfRangeException("Min can't be greater than max.");
        }

        $vals = array();

        $has_children = sizeof($node->children) > 0;

        $key_count = sizeof($node->keys);
        for ($i = 0; $i < $key_count; $i++) {
            if ($node->keys[$i]->key >= $min_key) {
                if ($has_children) {
                    $vals = array_merge($vals, $this->traverse($this->getChildNode($node, $i), $min_key, $max_key));
                }
                if ($node->keys[$i]->key <= $max_key) {
                    $vals[] = $node->keys[$i]->key;
                }
            }
        }

        if ($has_children && $node->keys[$i - 1]->key < $max_key) {
            $vals = array_merge($vals, $this->traverse($this->getChildNode($node, $i), $min_key, $max_key));
        }

        return $vals;
    }

    /**
     * @param $object
     * @return string
     */
    private function serialize($object)
    {
        return str_pad(serialize($object), self::NODE_SIZE_BYTES, '.');
    }


    /**
     * @throws \Exception
     */
    private function setFileHandler()
    {
        $mode = $this->overWrite == true ? 'w+' : 'r+';
        if (!($this->fileHandler = fopen($this->fileName, $mode))) {
            throw new RuntimeException('Unable to open file');
        }
    }

    /**
     * @return void
     */
    private function setRootNode()
    {
        if (fseek($this->fileHandler, -self::NODE_SIZE_BYTES, SEEK_END) !== -1) {
            $contents = fread($this->fileHandler, self::NODE_SIZE_BYTES);
            $this->rootNode = unserialize($contents);
        } else {
            $this->rootNode = new Node();
            $this->writeRootNode($this->rootNode);
        }
    }
}