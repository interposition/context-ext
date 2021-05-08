<?php declare(strict_types=1);

namespace Interposition\Context\Test;

use Interposition\Context;

class ExecuteTest2 extends Test
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
       return 'Simple execute to end.';
    }

    public function getDescription(): string
    {
        return 'Simple while cycle in inside. Execution in cycle. Generator: valid => next. Extension: finished => suspend. With the exchange of variables. Context switches: '.$this->count.'.';
    }

    public function callExt(): void
    {
        $i = $this->count;

        $obj = new Context(function () use ($i) {
            while ($i--){
                $r = Context::suspend($i);
            }
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
            while ($i--){
                $r = yield $i;
            }
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
}
