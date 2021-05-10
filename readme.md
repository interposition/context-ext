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
git checkout 0.1.1

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

### Perfomance
Simple performance tests written, see: perfomance.php. Results:
```
Tests iterations: 1000

Test: Real use test. Create object. Count: 1000.
Generator wrapped.
┌───────────┬──────────┬──────────┬──────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.001676 | 0.005227 | 0.001932 |
|───────────|──────────|──────────|──────────|
| Generator | 0.001549 | 0.004789 | 0.001798 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 108.20%  | 109.15%  | 107.45%  |
└───────────┴──────────┴──────────┴──────────┘

Test: Real use test. Execute coroutine (cycle). Switches count: 1000.
Generator wrapped.
┌───────────┬──────────┬──────────┬──────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000552 | 0.003445 | 0.000646 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000773 | 0.004107 | 0.000901 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 71.41%   | 83.88%   | 71.75%   |
└───────────┴──────────┴──────────┴──────────┘

Test: Real use test. Execute coroutine, recursive, suspend every level, levels: 1000. Generator use "yield from".
Generator wrapped.
┌───────────┬──────────┬──────────┬──────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000650 | 0.003519 | 0.000758 |
|───────────|──────────|──────────|──────────|
| Generator | 0.001510 | 0.005424 | 0.001747 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 43.04%   | 64.88%   | 43.39%   |
└───────────┴──────────┴──────────┴──────────┘
```
### Problems
## Cannot use suspend method in internal callback
The following code will cause a coredump.
```php
<?php declare(strict_types=1);

use Interposition\Context;

$context = new Context(function () {
    array_map(function () {
        Context::suspend();
    }, [1, 2]);
});

$context->resume();
```
### More
In draft...

### Changelog
## 0.1.1
-- Fix coredump when calling the resume method on the object that is destroyed before the context change.
-- Reality-based tests.

## 0.1.0
- First version of the worked extension.
- Basic tests.
