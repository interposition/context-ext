--TEST--
Parent namespace in closure
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;

$a = 1;
$b = 2;
$context = new Context(function () use ($a, &$b) {
    var_dump($a);
    var_dump($b);
    $a = 3;
    $b = 4;
});

$context->resume();
var_dump($a);
var_dump($b);
?>
--EXPECTF--
int(1)
int(2)
int(1)
int(4)
