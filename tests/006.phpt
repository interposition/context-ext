--TEST--
Run context in anther context
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;
$context = new Context(function () {
    $context = new Context(function () {

    });

    $context->resume();
});
$context->resume();
?>
--EXPECTF--
Fatal error: Uncaught RuntimeException: Could't resume context in another context! in %s:%d
Stack trace:
#0 %s(%d): Interposition\Context->resume()
#1 (0): {closure}()
#2 {main}
  thrown in %s on line %d
