<?php
/**
 * @author Mike Kaminski <michael.w.kaminski@gmail.com>
 * @since 5/4/2014
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (count($argv) != 4) {
    echo "\nMissing arguments. Format: php " . __FILE__ . " [file] [key] [value]\n\n";
    exit;
}

$filename = $argv[1];
$key = (int)$argv[2];
$value = $argv[3];

try {
    if (!file_exists($filename)) {
        if (touch($filename)) {
            echo "\n" . $filename . " was created.";
        } else {
            echo "\n" . $filename . " doesn't exist and wasn't able to be created";
            exit;
        }
    }
    $store = new \Kaminski\BTree\FileStore($filename, 3);
    $btree = new \Kaminski\BTree\BTree($store);

    $result = $btree->put($key, $value);

    echo "\nSuccessfully entered {" . $key . ", " . $value . "} into B-Tree";

} catch (\Exception $e) {
    echo "Error occurred: " . $e->getMessage();
    exit;
}

echo "\n\n";
