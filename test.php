<?php


require_once 'vendor/autoload.php';

use \Kaminski\BTree\FileStore;

$store = new FileStore('/tmp/db.txt', 3, true);
//$store = new \Kaminski\BTree\ArrayStore();
$tree = new \Kaminski\BTree\BTree($store, 3);

$count = 1000;

for($i = $count; $i >= 1; $i--) {
//for($i = 1; $i <= $count; $i++) {
    $tree->put($i, "val_!".$i);
}

die(print_r($tree->getKeyRange(1, 1000)));
//for($i = 1; $i <= $count; $i++) {
//    echo "\n".$tree->find($i)->value."\n";
//}
//die(print_r($store->getRootNode()));
//
//$store = new FileStore('/tmp/db.txt', 3, false);
////$store = new \Kaminski\BTree\ArrayStore();
//$tree = new \Kaminski\BTree\BTree($store, 3);
//
//for($i = 1000; $i >= 1; $i--) {
//    echo "\n".$tree->find($i)->value."\n";
//}