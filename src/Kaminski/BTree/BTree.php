<?php

namespace Kaminski\BTree;

class BTree
{
    const DEFAULT_ORDER = 3;

    /**
     * @var Node
     */
    private $rootNode;

    /**
     * The number of child nodes
     * @var int
     */
    private $order;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @param StoreInterface $store
     * @param int $order
     */
    public function __construct(StoreInterface $store, $order = self::DEFAULT_ORDER)
    {
        $this->order = $order;
        $this->store = $store;
        $this->rootNode = $this->store->getRootNode();
    }

    /**
     * @param $key
     * @param $value
     */
    public function put($key, $value)
    {
        $result = $this->insert($this->rootNode, $key, $value);

        if ($result !== null) {
            //Split root
            $n = new Node();
            $n->keys = array_splice($this->rootNode->keys, 1, 1);
            $this->store->writeChildNode($n, 0, $this->rootNode);
            $this->store->writeChildNode($n, 1, $result);
            $this->store->writeRootNode($n);
            $this->rootNode = $this->store->getRootNode();
        }
    }

    /**
     * @param $key
     * @return Entry|null
     */
    public function find($key)
    {
        return $this->search($this->rootNode, $key);
    }

    /**
     * @param Node $node
     * @param $key
     * @return Entry|null
     */
    private function search(Node $node, $key)
    {
        $key_count = count($node->keys);

        for ($i = 0; $i <= $key_count; $i++) {

            if ($i === $key_count || $key < $node->keys[$i]->key) {

                $result = $this->search($this->store->getChildNode($node, $i), $key);

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
        //TODO... write out notes after splicing them?
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

            $this->insertAt($node->keys, $i, new Entry($key, $value));

        } else {

            $key_count = count($node->keys);

            for ($i = 0; $i < $key_count; $i++) {

                if ($key < $node->keys[$i]->key) {

                    $result = $this->insert($this->store->getChildNode($node, $i), $key, $value);

                    if ($result !== null) {
                        $this->store->writeChildNode($node, $i + 1, $result);
                        //TODO... write out notes after splicing them?
                        $node->keys = array_merge($node->keys, array_splice($node->children[$i]->keys, 1, 1));
                        sort($node->keys);
                    }

                    break;
                }
            }

            if ($i === count($node->keys)) {
                $result = $this->insert($node->children[$i], $key, $value);
                if ($result !== null) {
                    $this->store->writeChildNode($node, $i + 1, $result);
                    //TODO... write out notes after splicing them?
                    $node->keys = array_merge($node->keys, array_splice($node->children[$i]->keys, 1, 1));
                }
            }
        }

        if (count($node->keys) === $this->order) {
            return $this->split($node);
        }

        return null;
    }

    /**
     * @param array $array
     * @param $index
     * @param $value
     */
    private function insertAt(array &$array, $index, $value)
    {
        $array_size = count($array);
        for ($j = $array_size; $j > $index; $j--) {
            $array[$j] = $array[$j - 1];
        }
        $array[$j] = $value;
    }

}