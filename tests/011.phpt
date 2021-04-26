--TEST--
Check pass variable outside context;
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;

$context = new Context(function () {
   Context::suspend();
   Context::suspend(1);
   $a = 2;
   $b = &$a;
   Context::suspend($b);
   var_dump($b);
});

var_dump($context->resume());
var_dump($context->resume());
var_dump($a = $context->resume());
$a = 3;
var_dump($context->resume());
?>
--EXPECTF--
NULL
int(1)
int(2)
int(2)
NULL
