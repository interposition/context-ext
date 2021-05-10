<?php declare(strict_types=1);

namespace Interposition\Context\Test;


use Interposition\Context;

class RealUseCaseTest extends Test
{
    public function getName(): string
    {
        return 'Real use test. Create object. Count: '.$this->count.'.';
    }

    public function getDescription(): string
    {
        return 'Generator wrapped.';
    }

    public function callExt(): void
    {
        $count = $this->count;

        while ($count--){
            $obj = new Context(function () {
                Context::suspend();
            });
        }
    }

    public function callGen(): void
    {
        $count = $this->count;

        while ($count--){
            $obj = new GenContext((function (){
                yield;
            })());
        }
    }
}
