--TEST--
Check if context is loaded
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
echo 'The extension "context" is available';
?>
--EXPECT--
The extension "context" is available
