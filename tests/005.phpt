--TEST--
Call finished context test
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;
$context = new Context(function () {});
$context->resume();
$context->resume();
?>
--EXPECTF--
Fatal error: Uncaught RuntimeException: Ctx completed or throwed! in %s:%d
Stack trace:
#0 %s(%d): Interposition\Context->resume()
#1 {main}
  thrown in %s on line %d
