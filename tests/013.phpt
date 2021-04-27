--TEST--
Check context final return val
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);

use Interposition\Context;

$context = new Context(function () {
    return 10;
});
var_dump($context->resume());

$context = new Context(function () {
});
var_dump($context->resume());
?>
--EXPECTF--
int(10)
NULL
