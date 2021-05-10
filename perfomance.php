<?php declare(strict_types=1);

require_once __DIR__.'/perfomance/autoload.php';

use Interposition\Context\Test\{RealUseCaseTest, RealUseCaseTest2, RealUseCaseTest3};


$runner = new Interposition\Context\Test\TestRunner(1000);

$runner->addTest(
    new RealUseCaseTest(1000),
    new RealUseCaseTest2(1000),
    new RealUseCaseTest3(1000),
);

echo $runner->run();
