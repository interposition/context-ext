--TEST--
Check context complete test
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;
$context = new Context(function () {});
var_dump($context->finished());
$context->resume();
var_dump($context->finished());
?>
--EXPECTF--
bool(false)
bool(true)
