--TEST--
Test resume method before release context object.
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;

(new Context(function () {
    echo 'ok';
}))->resume();
?>
--EXPECTF--
ok
