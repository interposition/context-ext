--TEST--
Call suspend outside context
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;
Context::suspend();
?>
--EXPECTF--
Fatal error: Uncaught RuntimeException: Could't suspend outside context! in %s:%d
Stack trace:
#0 %s(%d): Interposition\Context::suspend()
#1 {main}
  thrown in %s on line %d
