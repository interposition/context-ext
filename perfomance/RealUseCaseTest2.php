<?php declare(strict_types=1);

namespace Interposition\Context\Test;

use Interposition\Context;

class RealUseCaseTest2 extends Test
{
    public function getName(): string
    {
        return 'Real use test. Execute coroutine (cycle). Switches count: '.$this->count.'.';
    }

    public function getDescription(): string
    {
        return 'Generator wrapped.';
    }

    public function callExt(): void
    {

        $count = $this->count;

        $obj = new Context(function () use($count) {
            while ($count--){
                $in = Context::suspend($count);
            }
        });

        $i = 0;

        while (!$obj->finished()){
            $out = $obj->resume($i++);
        }
    }

    public function callGen(): void
    {
        $count = $this->count;

        $obj = new GenContext((function () use($count) {
            while ($count--){
                $in = yield $count;
            }
        })());

        $i = 0;

        while (!$obj->finished()){
            $out = $obj->resume($i++);
        }
    }
}
