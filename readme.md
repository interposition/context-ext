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

### Perfomance
Simple performance tests written, see: perfomance.php. Results:
```
Tests iterations: 1000

Test: Simple execute to end.
Simple while cycle in inside. Execution in cycle.
Generator: valid => next. Extension: finished => suspend.
No variable exchange. Context switches: 1000.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000279 | 0.003452 | 0.000353 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000199 | 0.002813 | 0.000244 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 140.29%  | 122.72%  | 144.97%  |
└────────────────────────────────────────────┘

Test: Simple execute to end.
Simple while cycle in inside. Execution in cycle.
Generator: valid => next. Extension: finished => suspend.
With the exchange of variables. Context switches: 1000.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000392 | 0.002731 | 0.000474 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000297 | 0.003332 | 0.000361 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 131.94%  | 81.96%   | 131.29%  |
└────────────────────────────────────────────┘

Test: Recursive execute to end.
Recursive call test. Extension - recursive call function.
Generator - recursive call generator with "yield from".
Switch context on every level. No variable exchange. Levels: 1.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000003 | 0.000037 | 0.000004 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000003 | 0.002006 | 0.000007 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 100.00%  | 1.84%    | 57.12%   |
└────────────────────────────────────────────┘

Test: Recursive execute to end.
Recursive call test. Extension - recursive call function.
Generator - recursive call generator with "yield from".
Switch context on every level. No variable exchange. Levels: 10.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000006 | 0.000487 | 0.000008 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000010 | 0.000079 | 0.000012 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 60.98%   | 617.22%  | 71.03%   |
└────────────────────────────────────────────┘

Test: Recursive execute to end.
Recursive call test. Extension - recursive call function.
Generator - recursive call generator with "yield from".
Switch context on every level. No variable exchange. Levels: 100.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000036 | 0.002694 | 0.000045 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000071 | 0.002402 | 0.000092 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 50.84%   | 112.15%  | 49.43%   |
└────────────────────────────────────────────┘

Test: Recursive execute to end.
Recursive call test. Extension - recursive call function.
Generator - recursive call generator with "yield from".
Switch context on every level.  With the exchange of variables. Levels: 1.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000003 | 0.000036 | 0.000004 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000003 | 0.000023 | 0.000004 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 100.00%  | 155.67%  | 104.11%  |
└────────────────────────────────────────────┘

Test: Recursive execute to end.
Recursive call test. Extension - recursive call function.
Generator - recursive call generator with "yield from".
Switch context on every level. With the exchange of variables. Levels: 10.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000007 | 0.000078 | 0.000009 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000011 | 0.001937 | 0.000016 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 63.04%   | 4.02%    | 56.78%   |
└────────────────────────────────────────────┘

Test: Recursive execute to end.
Recursive call test. Extension - recursive call function.
Generator - recursive call generator with "yield from".
Switch context on every level.  With the exchange of variables. Levels: 100.

┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.000046 | 0.001868 | 0.000057 |
|───────────|──────────|──────────|──────────|
| Generator | 0.000079 | 0.002521 | 0.000104 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 58.01%   | 74.10%   | 55.10%   |
└────────────────────────────────────────────┘

Test: Create instance.
Create context obj vs create anonymous function. Objects created in loop: 10000.
┌────────────────────────────────────────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.011884 | 0.019595 | 0.013698 |
|───────────|──────────|──────────|──────────|
| Generator | 0.002413 | 0.005433 | 0.002920 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 492.49%  | 360.66%  | 469.10%  |
└────────────────────────────────────────────┘

Test: Create instance.
Create context obj vs create generator. Objects created in loop: 10000.
┌───────────┬──────────┬──────────┬──────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.011775 | 0.061873 | 0.014947 |
|───────────|──────────|──────────|──────────|
| Generator | 0.006537 | 0.037087 | 0.008290 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 180.13%  | 166.83%  | 180.31%  |
└───────────┴──────────┴──────────┴──────────┘

Test: Create instance.
Create context obj vs create anonymous function. Pass variable in use.
Objects created in loop: 10000.

┌───────────┬──────────┬──────────┬──────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.015596 | 0.053088 | 0.019060 |
|───────────|──────────|──────────|──────────|
| Generator | 0.004869 | 0.029522 | 0.006351 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 320.31%  | 179.83%  | 300.12%  |
└───────────┴──────────┴──────────┴──────────┘

Test: Create instance.
Create context obj vs create generator.
Pass variable in use. Objects created in loop: 10000.

┌───────────┬──────────┬──────────┬──────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.015301 | 0.099350 | 0.020127 |
|───────────|──────────|──────────|──────────|
| Generator | 0.009635 | 0.050150 | 0.012816 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 158.81%  | 198.11%  | 157.05%  |
└───────────┴──────────┴──────────┴──────────┘

Test: Create instance.
Create context obj vs create generator.
Pass variable in use to context, for generator - in func argument.
Objects created in loop: 10000.

┌───────────┬──────────┬──────────┬──────────┐
|           | Min      | Max      | Avg      |
|───────────|──────────|──────────|──────────|
| Extension | 0.015490 | 0.057620 | 0.018237 |
|───────────|──────────|──────────|──────────|
| Generator | 0.006858 | 0.018640 | 0.008473 |
|───────────|──────────|──────────|──────────|
| (E/G)%    | 225.86%  | 309.12%  | 215.24%  |
└───────────┴──────────┴──────────┴──────────┘
```
### Problems
In draft...
### More
In draft...

### Changelog
