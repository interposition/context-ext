<?php declare(strict_types=1);

namespace Interposition\Context\Test;

use Generator;
use Interposition\Context;

class ExecuteDepthTest2 extends Test
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
        return  'Recursive call test. Extension - recursive call function. Generator - recursive call generator with "yield from". Switch context on every level.  With the exchange of variables.  Levels: '.$this->count.'.';
    }

    public function callExt(): void
    {
        $i = $this->count;

        $obj = new Context(function () use ($i) {
            $this->callExtInternal($i);
        });

        $i = 0;
        while (!$obj->finished()){
            $r = $obj->resume($i++);
        }
    }

    public function callGen(): void
    {
        $i = $this->count;

        $obj = function () use($i) {
            yield from $this->callGenInternal($i);
        };

        $gen = $obj();

        $i = 0;
        $r = $gen->current();

        while ($gen->valid()){
            $gen->send($i++);
            $gen->next();
            $r = $gen->current();
        }
    }


    private function callExtInternal(int $i): void
    {
        if($i){
            $this->callExtInternal($i - 1);
        }

        Context::suspend($i);
    }

    private function callGenInternal(int $i): Generator
    {
        if($i){
            yield from $this->callGenInternal($i - 1);
        }

        yield $i;
    }

}
