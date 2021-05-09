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
        if($this->started){
            $this->generator->send($val);
            $this->generator->next();
        }

        $this->started = true;

        return $this->generator->current();
    }

    public function finished(): bool
    {
        return !$this->generator->valid();
    }
}
