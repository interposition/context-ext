<?php declare(strict_types=1);

namespace Interposition\Context\Test;

use Generator;
use Interposition\Context;

class ExecuteDepthTest extends Test
{
    private int $count;

    public function __construct(int $count)
    {
        if($count < 1){
            throw new \InvalidArgumentException('Count must be above or equal 1.');
        }

        $this->count = $count;
    }

    public function getName(): string
    {
       return 'Recursive execute to end.';
    }

    public function getDescription(): string
    {
        return  'Recursive call test. Extension - recursive call function. Generator - recursive call generator with "yield from". Switch context on every level. No variable exchange. Levels: '.$this->count.'.';
    }

    public function callExt(): void
    {
        $i = $this->count;

        $obj = new Context(function () use ($i) {
            $this->callExtInternal($i);
        });

        while (!$obj->finished()){
            $obj->resume();
        }
    }

    public function callGen(): void
    {
        $i = $this->count;

        $obj = function () use($i) {
            yield from $this->callGenInternal($i);
        };

        $gen = $obj();

        while ($gen->valid()){
            $gen->next();
        }
    }


    private function callExtInternal(int $i): void
    {
        if($i){
            $this->callExtInternal($i - 1);
        }

        Context::suspend();
    }

    private function callGenInternal(int $i): Generator
    {
        if($i){
            yield from $this->callGenInternal($i - 1);
        }

        yield;
    }

}
