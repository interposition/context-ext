<?php declare(strict_types=1);

namespace Interposition\Context\Test;

abstract class Test
{
    protected int $count;

    public function __construct(int $count)
    {
        if($count < 1){
            throw new \InvalidArgumentException('Count must be above or equal 1.');
        }

        $this->count = $count;
    }

    abstract public function getName(): string;
    abstract public function getDescription(): string;
    abstract public function callExt(): void;
    abstract public function callGen(): void;

    public function call(): TestResult
    {
        $timer   = new Timer();
        $extTime = null;
        $genTime = null;

        $timer->tick();

        $this->callExt();
        $extTime = $timer->tick();

        $this->callGen();
        $genTime = $timer->tick();

        return new TestResult($extTime, $genTime);
    }
}
