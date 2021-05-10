<?php declare(strict_types=1);

namespace Interposition\Context\Test;

use Generator;

class GenContext
{
    private Generator $generator;
    private bool $started = false;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function resume(mixed $val = null): mixed
    {
        if(!$this->started){
            $this->started = true;
            return $this->generator->current();
        }

        return $this->generator->send($val);
    }

    public function finished(): bool
    {
        return !$this->generator->valid();
    }
}
