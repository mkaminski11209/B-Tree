<?php
/**
 * @author Mike Kaminski <michael.w.kaminski@gmail.com>
 * @since 5/4/2014
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (count($argv) != 3) {
    echo "\nMissing arguments. Format: php " . __FILE__ . " [file] [key]\n\n";
    exit;
}

$filename = $argv[1];
$key = (int)$argv[2];

try {
    $store = new \Kaminski\BTree\FileStore($filename, 3);
    $btree = new \Kaminski\BTree\BTree($store);

    $result = $btree->find($key);

    if ($result !== null) {
        echo "Entry found found for `" . $key . "`: " . $result->value . "\n";
    } else {
        echo "No result found for " . $key . "\n";
    }

} catch (\Exception $e) {
    echo "Error occurred: " . $e->getMessage();
    exit;
}

echo "\n\n";