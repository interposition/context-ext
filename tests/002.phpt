--TEST--
Context creation with props check
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;
$context = new Context(function ($prop) {});
?>
--EXPECTF--
Fatal error: Uncaught Error: Interposition\Context::__construct(): Argument #1 ($closure) must have no arguments. in %s:%d
Stack trace:
#0 %s(%d): Interposition\Context->__construct(Object(Closure))
#1 {main}
  thrown in %s on line %d
