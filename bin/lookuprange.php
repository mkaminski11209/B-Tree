<?php
/**
 * @author Mike Kaminski <michael.w.kaminski@gmail.com>
 * @since 5/4/2014
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (count($argv) != 4) {
    echo "\nMissing arguments. Format: php " . __FILE__ . " [file] [lowkey] [hikey]\n\n";
    exit;
}

$filename = $argv[1];
$low_key = (int)$argv[2];
$high_key = (int)$argv[3];

try {
    $store = new \Kaminski\BTree\FileStore($filename, 3);
    $btree = new \Kaminski\BTree\BTree($store);

    $result = $btree->getKeyRange($low_key, $high_key);

    if (count($result) > 0) {
        echo "\nResults for " . $filename . " from key " . $low_key . " to " . $high_key . "\n\n";
        foreach ($result as $entry) {
            echo $entry . "\n";
        }
    } else {
        echo "No result(s) found.";
    }

} catch (\Exception $e) {
    echo "Error occurred: " . $e->getMessage();
    exit;
}

echo "\n\n";