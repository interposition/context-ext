<?php declare(strict_types=1);

namespace Interposition\Context\Test;

use Generator;
use Interposition\Context;

class RealUseCaseTest3 extends Test
{
    public function getName(): string
    {
        return 'Real use test. Execute coroutine, recursive, suspend every level, levels: '.$this->count.'. Generator use "yield from".';
    }

    public function getDescription(): string
    {
        return 'Generator wrapped.';
    }

    public function callExt(): void
    {
        $obj = new Context(function () {
            $this->extRec($this->count);
        });

        $i = 0;

        while (!$obj->finished()){
            $out = $obj->resume($i++);
        }
    }

    public function callGen(): void
    {
        $obj = new GenContext($this->genRec($this->count));

        $i = 0;

        while (!$obj->finished()){
            $out = $obj->resume($i++);
        }
    }

    private function extRec(int $level): void
    {
        $in = Context::suspend($level);

        if($level){
            $this->extRec(--$level);
        }
    }

    private function genRec(int $level): Generator
    {
        $in = yield $level;

        if($level){
            yield from $this->genRec(--$level);
        }
    }
}
