<?php declare(strict_types=1);

require_once __DIR__.'/perfomance/autoload.php';

use Interposition\Context\Test\{CreateTest,
    CreateTest2,
    CreateTest3,
    CreateTest4,
    CreateTest5,
    ExecuteDepthTest,
    ExecuteDepthTest2,
    ExecuteTest,
    ExecuteTest2,
};


$runner = new Interposition\Context\Test\TestRunner(1000);

$runner->addTest(
    new ExecuteTest(1000),
    new ExecuteTest2(1000),
    new ExecuteDepthTest(1),
    new ExecuteDepthTest(10),
    new ExecuteDepthTest(100),
    new ExecuteDepthTest2(1),
    new ExecuteDepthTest2(10),
    new ExecuteDepthTest2(100),
    new CreateTest(10000),
    new CreateTest2(10000),
    new CreateTest3(10000),
    new CreateTest4(10000),
    new CreateTest5(10000),
);

echo $runner->run();
