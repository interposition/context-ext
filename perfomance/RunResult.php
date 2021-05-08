<?php declare(strict_types=1);

namespace Interposition\Context\Test;

interface RunResult
{
    public function getName(): string;
    public function getDescription(): string;
    public function getIterations(): int;
    public function getMinExtTime(): float;
    public function getMaxExtTime(): float;
    public function getAvgExtTime(): float;
    public function getMinGenTime(): float;
    public function getMaxGenTime(): float;
    public function getAvgGenTime(): float;
}
