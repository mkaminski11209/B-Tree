<?php
/**
 * B-Tree File Store
 *
 * Supports 4KB 3, 5, 7...-ary nodes
 *
 * @author Mike Kaminski <michael.w.kaminski@gmail.com>
 * @since 5/6/2014
 */
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
     * @var int
     */
    private $maxKeys;

    /**
     * @var int
     */
    private $seekCount;

    /**
     * @param string $file_name Full path to database file
     * @param int $max_keys The order of the B-Tree
     */
    public function __construct($file_name, $max_keys)
    {
        $this->fileName = $file_name;
        $this->maxKeys = $max_keys;
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
        if(!count($node->children) || !isset($node->children[$index])) {
            return null;
        }
        $child_offset = $node->children[$index];
        $this->fseek($this->fileHandler, $child_offset);
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
        $this->fseek($this->fileHandler, $child_offset);
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
        $this->fseek($this->fileHandler, -self::NODE_SIZE_BYTES, SEEK_END);
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
            $this->fseek($this->fileHandler, $node->offset);
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
        $this->fseek($this->fileHandler, 0, SEEK_END);
        fwrite($this->fileHandler, $this->serialize($node), self::NODE_SIZE_BYTES);

        $node->offset = ftell($this->fileHandler) - self::NODE_SIZE_BYTES;
        $this->rootNode = $node;
    }

    /**
     * @param Node $node
     * @param $min_key
     * @param $max_key
     * @throws \OutOfRangeException
     * @return Entry[]
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
                    $vals[] = $node->keys[$i];
                }
            }
        }

        if ($has_children && $node->keys[$i - 1]->key < $max_key) {
            $vals = array_merge($vals, $this->traverse($this->getChildNode($node, $i), $min_key, $max_key));
        }

        return $vals;
    }

    /**
     * @return int
     */
    public function getSeekCount() {
        return $this->seekCount;
    }

    /**
     * @return void
     */
    public function resetSeekCount() {
        $this->seekCount = 0;
    }

    /**
     * @param $resource
     * @param $offset
     * @param int $whence
     * @return int
     */
    private function fseek($resource, $offset, $whence = SEEK_SET) {
        $this->seekCount++;
        return fseek($resource, $offset, $whence);
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
     * @throws \RuntimeException
     */
    private function setFileHandler()
    {
        //
        // Open for reading and writing; place the file pointer at the end of
        // the file. If the file does not exist, attempt to create it.
        //
        if (!($this->fileHandler = fopen($this->fileName, 'r+'))) {
            throw new RuntimeException('Unable to open file '.$this->fileName);
        }
    }

    /**
     * @return void
     */
    private function setRootNode()
    {
        if ($this->fseek($this->fileHandler, -self::NODE_SIZE_BYTES, SEEK_END) !== -1) {
            $contents = fread($this->fileHandler, self::NODE_SIZE_BYTES);
            $this->rootNode = unserialize($contents);
        } else {
            $this->rootNode = new Node();
            $this->writeRootNode($this->rootNode);
        }
    }

    /**
     * @return int
     */
    public function getMaxKeys()
    {
        return $this->maxKeys;
    }
}