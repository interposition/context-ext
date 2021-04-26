--TEST--
Context creation without props check
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;
$context = new Context(function () {});
?>
--EXPECTF--
