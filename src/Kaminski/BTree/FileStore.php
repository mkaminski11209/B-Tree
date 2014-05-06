<?php

namespace Kaminski\BTree;

class FileStore implements StoreInterface
{

    const NODE_SIZE_BYTES = 4096;

    private $fileName;

    private $fileHandler;

    private $maxChildren;

    /**
     * @var Node
     */
    private $rootNode;

    private $clear;

    public function __construct($file_name, $max_children, $clear)
    {
        $this->fileName = $file_name;
        $this->maxChildren = $max_children;
        $this->clear = $clear;
        $this->setFileHandler();
        $this->setRootNode();
    }

    /**
     * @throws \Exception
     */
    private function setFileHandler()
    {
        $mode = $this->clear == true ? 'w+' : 'r+';
        if (!($this->fileHandler = fopen($this->fileName, $mode))) {
            throw new \Exception('Unable to open file');
        }
    }

    private function setRootNode()
    {
        if (fseek($this->fileHandler, -self::NODE_SIZE_BYTES, SEEK_END) !== -1) {
            echo 'Found root node...';
            $contents = fread($this->fileHandler, self::NODE_SIZE_BYTES);
            $this->rootNode = unserialize($contents);
        } else {
            echo 'Creating new root node...';
            $this->rootNode = new Node();

            $this->writeRootNode($this->rootNode);

        }
    }

    /**
     * @return mixed
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * @param Node $node
     * @param int $index
     * @return mixed
     */
    public function getChildNode(Node $node, $index)
    {
        $child_offset = $node->children[$index];
        fseek($this->fileHandler, $child_offset);
        $contents = fread($this->fileHandler, self::NODE_SIZE_BYTES);
        return unserialize($contents);
    }

    /**
     * @param Node $parent_node
     * @param int $index
     * @param Node $child_node
     * @return mixed
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

    public function allocateNode(Node $node)
    {
        fseek($this->fileHandler, -self::NODE_SIZE_BYTES, SEEK_END);
        $node->offset = ftell($this->fileHandler);
        fwrite($this->fileHandler, $this->serialize($node), self::NODE_SIZE_BYTES);

        $this->writeRootNode($this->getRootNode());
    }

    public function writeNode(Node $node)
    {
        if (count($node->keys) > $this->maxChildren) {
            throw new \Exception('Node capacity exceeded, you must split.');
        }
        if (is_int($node->offset)) {
            fseek($this->fileHandler, $node->offset);
            fwrite($this->fileHandler, $this->serialize($node), self::NODE_SIZE_BYTES);
        } else {
            throw new \Exception('Requires offset');
        }
    }


    /**
     * @param Node $node
     * @return mixed
     */
    public function writeRootNode(Node $node)
    {
        fseek($this->fileHandler, 0, SEEK_END);
        fwrite($this->fileHandler, $this->serialize($node), self::NODE_SIZE_BYTES);

        $node->offset = ftell($this->fileHandler) - self::NODE_SIZE_BYTES;
        $this->rootNode = $node;
    }

    private function serialize($object)
    {
        $serialized = serialize($object) . ' ' . print_r($object, true);
        return str_pad($serialized, self::NODE_SIZE_BYTES, '.');
    }
}