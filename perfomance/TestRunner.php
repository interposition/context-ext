<?php declare(strict_types=1);

namespace Interposition\Context\Test;

class TestRunner
{
    private int $iterations;

    /** @var Test[]*/
    private array $tests = [];

    public function __construct(int $iterations)
    {
        if($iterations < 1){
            throw new \InvalidArgumentException('Iterations count must be above or equal 1.');
        }

        $this->iterations = $iterations;
    }

    public function addTest(Test ... $tests): self
    {
        $this->tests = array_merge($this->tests, $tests);

        return $this;
    }

    public function run(): string
    {
        if($gc = gc_enabled()){
            gc_disable();
        }

        $out = ['Tests iterations: '.$this->iterations];

        foreach ($this->tests as $test){
            $result = $this->runTest($test);


            $this->formatOut($result, $out);
        }

        if($gc){
            gc_enable();
        }

        $out[] = '';

        return implode("\n", $out);
    }

    private function runTest(Test $test): RunResult
    {
        $iterations = $this->iterations;
        $result     = new RunResultImpl($test);

        while ($iterations){

            $result->addResult($test->call());

            $iterations--;
            gc_collect_cycles();
        }

        return $result;
    }

    private function formatOut(RunResult $result, array &$out): void
    {
        $out[] = '';
        $out[] = 'Test: '.$result->getName();
        $out[] = $result->getDescription();

        $out[] = TableRenderer::render([
            ['', 'Min', 'Max', 'Avg'],
            [
                'Extension',
                $this->formatTime($result->getMinExtTime()),
                $this->formatTime($result->getMaxExtTime()),
                    $this->formatTime($result->getAvgExtTime()),
            ],
            [
                'Generator',
                $this->formatTime($result->getMinGenTime()),
                $this->formatTime($result->getMaxGenTime()),
                $this->formatTime($result->getAvgGenTime()),
            ],
            [
                '(E/G)%',
                $this->percentage($result->getMinExtTime(), $result->getMinGenTime()),
                $this->percentage($result->getMaxExtTime(), $result->getMaxGenTime()),
                $this->percentage($result->getAvgExtTime(), $result->getAvgGenTime()),
            ]
        ]);
/*
        $this->formatOutNumberLine($out, $result->getMinExtTime(), $result->getMinGenTime(), 'Min: ');
        $this->formatOutNumberLine($out, $result->getMaxExtTime(), $result->getMaxGenTime(), 'Max: ');
        $this->formatOutNumberLine($out, $result->getAvgExtTime(), $result->getAvgGenTime(), 'Avg: ');
        */
    }

    private function formatTime(float $time): string
    {
        return number_format($time, 6, '.', '');
    }

    private function percentage(float $one, float $two): string
    {
        if(!$two){
            return '-';
        }

        return number_format($one / $two * 100, 2, '.', '') .'%';
    }
}
