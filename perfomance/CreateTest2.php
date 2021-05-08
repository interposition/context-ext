<?php declare(strict_types=1);

namespace Interposition\Context\Test;

use Interposition\Context;

class CreateTest2 extends Test
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
        return 'Create instance.';
    }

    public function getDescription(): string
    {
        return 'Create context obj vs create generator. Objects created in loop: '.$this->count.'.';
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
            $obj = function () {
                yield;
            };

            $gen = $obj();

            $count--;
        }
    }
}
