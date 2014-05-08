B-Tree
======

B-Tree Implementation in PHP

Requirements
-PHP 5.3+
-PHPUnit (for running unit tests, if desired)

Directory Structure
/bin        Client programs [insert, lookup, lookup range]
/src        B-Tree classes
/tests      PHPUnit tests
/vendor     Class autoloader logic

Example commands

Insert

    php bin/insert.php /tmp/db1.txt 5 "Some value"

Find [Requires blank text file for new database]

    php bin/lookup.php /tmp/db1.txt 5

Lookup Range

    php bin/lookuprange.php /tmp/db1.txt 5 20