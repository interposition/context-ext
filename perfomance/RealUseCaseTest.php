<?php declare(strict_types=1);

namespace Interposition\Context\Test;


use Interposition\Context;

class RealUseCaseTest extends Test
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
        while ($count){
            $obj = new Context(function () {
                Context::suspend();
            });

            $count--;
        }
    }

    public function callGen(): void
    {

        $count = $this->count;
        while ($count){
            $obj = new GenContext((function (){
                yield;
            })());

            $count--;
        }
    }
}
