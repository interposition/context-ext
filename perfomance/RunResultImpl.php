<?php declare(strict_types=1);

namespace Interposition\Context\Test;

class RunResultImpl implements RunResult
{
    private int $iterations = 0;

    private ?float $minExt = null;
    private ?float $minGen = null;
    private ?float $maxExt = null;
    private ?float $maxGen = null;
    private ?float $sumExt = null;
    private ?float $sumGen = null;

    private Test $test;

    public function __construct(Test $test)
    {
        $this->test = $test;
    }

    public function getName(): string
    {
        return $this->test->getName();
    }

    public function getDescription(): string
    {
        return $this->test->getDescription();
    }

    public function addResult(TestResult $result): void
    {
        $this->iterations++;

        $extTime = $result->getExtTime();

        if($this->minExt === null || $this->minExt > $extTime){
            $this->minExt = $extTime;
        }

        if($this->maxExt === null || $this->maxExt < $extTime){
            $this->maxExt = $extTime;
        }

        $this->sumExt += $extTime;

        $genTime = $result->getGenTime();

        if($this->minGen === null || $this->minGen > $genTime){
            $this->minGen = $genTime;
        }

        if($this->maxGen === null || $this->maxGen < $genTime){
            $this->maxGen = $genTime;
        }

        $this->sumGen += $genTime;
    }

    public function getIterations(): int
    {
        return $this->iterations;
    }

    public function getMinExtTime(): float
    {
        return $this->minExt ?? 0;
    }

    public function getMaxExtTime(): float
    {
        return $this->maxExt ?? 0;
    }

    public function getAvgExtTime(): float
    {
        return $this->iterations ? ($this->sumExt / $this->iterations) : 0;
    }

    public function getMinGenTime(): float
    {
        return $this->minGen ?? 0;
    }

    public function getMaxGenTime(): float
    {
        return $this->maxGen ?? 0;
    }

    public function getAvgGenTime(): float
    {
        return $this->iterations ? ($this->sumGen / $this->iterations) : 0;
    }
}
