<?php declare(strict_types=1);

namespace Interposition\Context\Test;

class TestResult
{
    private float $extTime;
    private float $genTime;

    public function __construct(float $extTime, float $genTime)
    {
        $this->extTime = $extTime;
        $this->genTime = $genTime;
    }

    public function getGenTime(): float
    {
        return $this->genTime;
    }

    public function getExtTime(): float
    {
        return $this->extTime;
    }
}
