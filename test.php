<?php


require_once 'vendor/autoload.php';

use \Kaminski\BTree\FileStore;

$file = '/tmp/db.txt';

if(file_exists($file)) {
    unlink($file);
}

touch($file);

$store = new FileStore($file, 3);
//$store = new \Kaminski\BTree\ArrayStore();
$tree = new \Kaminski\BTree\BTree($store);

$count = 1000;

//for($i = $count; $i >= 1; $i--) {
for($i = 1; $i <= $count; $i++) {
    $tree->put($i, "val_!".$i);
}
//
//$store = new FileStore('/tmp/db.txt', 3);
////$store = new \Kaminski\BTree\ArrayStore();
//$tree = new \Kaminski\BTree\BTree($store);

$store->resetSeekCount();

print_r($tree->getKeyRange(70, 91));

echo 'Seeks: '.$store->getSeekCount();
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