--TEST--
Check depth fcall
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);

use Interposition\Context;

function f1(){
    Context::suspend(1);
}

function f2(){
    f1();
    Context::suspend(2);
}

function f3(){
    f2();
    Context::suspend(3);
}

$context = new Context(function () {
    f3();
});
var_dump($context->resume());
var_dump($context->resume());
var_dump($context->resume());
$context->resume();
?>
--EXPECTF--
int(1)
int(2)
int(3)
