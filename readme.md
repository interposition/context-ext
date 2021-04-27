# PHP Coroutines

A simple extension that implements the functionality of the coroutines in PHP.
Designed for teaching the development of php extensions, and learning the zend core.

The extension has no practical value - in php 8.1 will appear [fibers](https://wiki.php.net/rfc/fibers).

## Specification

- Zend stack used.
- Can't run a coroutine in another coroutine.
- Can't run coroutine twice.
- Can't define coroutine function with params.
- Namespace used for extension.
- Fixed stack size: 8kb.
- Non-thread safe.
- PHP 8.0.0 - minimum version.

## Installation

Get from version control, compile and install

```
git clone https://github.com/interposition/context-ext.git

# optional
git checkout 0.1.0

cd context-ext
phpize
./configure
make

#optional
make test

sudo make install
```
Define in your .ini file:

```
extension=context
```
### Using

#### Create coroutine
```php
<?php declare(strict_types=1);

use Interposition\Context;

// Will throw an exception
$coroutine = new Context(function ($a) {
    // code here
});

// But you can pass parameters from the current scope
$a = 1;
$b = 2;

$coroutine = new Context(function () use ($a, &$b) {
    // code here
});

// Or object scope
class A
{
    private int $i = 1;

    public function getCoroutine(): Context
    {
        return new Context(function () {
            echo $this->i; // echo 1
        });
    }
}
```

### Run and suspend

For start or resume coroutine - call resume method. Call suspend for return to main context.
```php
<?php declare(strict_types=1);

use Interposition\Context;

$coroutine = new Context(function () {
    Context::suspend();
});

$coroutine->resume();

```

### Check status

For check coroutine status - call finished method.

```php
<?php declare(strict_types=1);

use Interposition\Context;

$coroutine = new Context(function () {

});

// return false
$coroutine->finished();
$coroutine->resume();
// return true
$coroutine->finished();
// Will throw an exception
$coroutine->resume();

```

### Data exchange
To exchange data between parent and child context
\- you can pass the parameter in resume or suspend method.
If the method is called without a parameter, it will return null.

```php
<?php declare(strict_types=1);

use Interposition\Context;

$coroutine = new Context(function () {
    // $a = int(2)
    $a = Context::suspend(5);
    // $b = null
    $b = Context::suspend();

    return 100;
});

// The sent parameter will be skipped. $a = int(5).
$a = $coroutine->resume(1);
// $b = null
$b = $coroutine->resume(2);
// $c = int(100)
$c = $coroutine->resume();
```

### Problems
In draft...
### More
In draft...
