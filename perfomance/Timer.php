<?php declare(strict_types=1);

namespace Interposition\Context\Test;

class Timer
{
    private ?float $last = null;

    public function tick(): float
    {
        $last = $this->last;
        $this->last = microtime(true);

        return $last ? $this->last - $last : 0;
    }
}
