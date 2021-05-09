<?php declare(strict_types=1);

namespace Interposition\Context\Test;


use Interposition\Context;

class RealUseCaseTest2 extends Test
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
        return 'Real use test. Create object.';
    }

    public function getDescription(): string
    {
        return 'Generator wrapped.';
    }

    public function callExt(): void
    {

        $count = $this->count;

        $obj = new Context(function () use($count) {
            while ($count){
                $r = Context::suspend($count);
                $count--;
            }
        });

        while (!$obj->finished()){
            $r = $obj->resume($count--);
        }
    }

    public function callGen(): void
    {
        $count = $this->count;

        $obj = new GenContext((function () use($count) {
            while ($count){
                $r = yield $count;
                $count--;
            }
        })());

        while (!$obj->finished()){
            $r = $obj->resume($count--);
        }
    }
}
