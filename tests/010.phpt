--TEST--
Check pass variable in context;
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;

$context = new Context(function () {
    $c = Context::suspend();
    var_dump($c);
    $c = Context::suspend();
    var_dump($c);
    $c = 3;
    $c = Context::suspend();
    var_dump($c);
});

$context->resume();
$context->resume(1);
$a = 2;
$b = &$a;
$context->resume($b);
var_dump($b);
$context->resume();
?>
--EXPECTF--
int(1)
int(2)
int(2)
NULL
