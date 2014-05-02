<?php

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

class BTree
{

    private $root;

    const MAX_CHILDREN = 3;

    public function __construct()
    {
        $this->root = new Node();
    }

    public function put($key, $value)
    {
        $result = $this->insert($this->root, $key, $value);

        if ($result !== null) {
            //Split root
            $n = new Node();
            $n->children[] = $this->root;
            $n->children[] = $result;
            $n->keys = array_splice($this->root->keys, 1, 1);
            $this->root = $n;
            $this->root->leaf = false;
        }
    }

    public function find($key)
    {
        return $this->search($this->root, $key);
    }

    /**
     * @param Node $node
     * @param $key
     * @return Entry|null
     */
    private function search(Node $node, $key)
    {
        for ($i = 0; $i <= count($node->keys); $i++) {
            if ($i === count($node->keys) || $key < $node->keys[$i]->key) {
                $result = $this->search($node->children[$i], $key);
                if ($result !== null) {
                    return $result;
                }
            } else if ($key === $node->keys[$i]->key) {
                return $node->keys[$i];
            }
        }

        return null;
    }

    /**
     * Split node returning new second node
     * @param Node $node
     * @return Node
     */
    private function split(Node $node)
    {
        $new_root = new Node();
        $new_root->keys = array_splice($node->keys, 2);
        $new_root->children = count($node->children) > 2 ? array_splice($node->children, 2) : array();

        return $new_root;
    }

    /**
     * @param Node $node
     * @param $key
     * @param $value
     * @return Node|null
     */
    private function insert(Node $node, $key, $value)
    {
        if (count($node->children) === 0) {
            $count = count($node->keys);
            for ($i = 0; $i < $count; $i++) {
                if ($key < $node->keys[$i]->key) {
                    break;
                }
            }

            $count = count($node->keys);
            //Shift the keys over to make room
            for ($j = $count; $j > $i; $j--) {
                $node->keys[$j] = $node->keys[$j - 1];
            }

            $node->keys[$i] = new Entry($key, $value);

        } else {
            for ($i = 0; $i < count($node->keys); $i++) {
                if ($key < $node->keys[$i]->key) {

                    $result = $this->insert($node->children[$i], $key, $value);

                    if ($result !== null) {

                        for ($j = count($node->children); $j > ($i + 1); $j--) {
                            $node->children[$j] = $node->children[$j - 1];
                        }

                        $node->children[$j] = $result;

                        $node->keys = array_merge($node->keys, array_splice($node->children[$i]->keys, 1, 1));
                        sort($node->keys);
                    }
                    break;
                }
            }
            if ($i === count($node->keys)) {
                $result = $this->insert($node->children[$i], $key, $value);
                if ($result !== null) {
                    $node->children[] = $result;
                    $node->keys = array_merge($node->keys, array_splice($node->children[$i]->keys, 1, 1));
                }
            }
        }

        if (count($node->keys) === self::MAX_CHILDREN) {
            return $this->split($node);
        }

        return null;
    }

}