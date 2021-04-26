--TEST--
Check this;
--SKIPIF--
<?php include __DIR__.DIRECTORY_SEPARATOR.'exclude.php';?>
--FILE--
<?php declare(strict_types=1);
use Interposition\Context;

class A
{
    private int $a = 1;

    public function getContext(): Context
    {
        return new Context(function () {
            var_dump($this->getVar());
            $this->setVar(2);
        });
    }

    public function getVar(): int
    {
        return $this->a;
    }

    public function setVar(int $v): void
    {
        $this->a = $v;
    }
}

$a = new A;
$context = $a->getContext();
$context->resume();
var_dump($a->getVar());
?>
--EXPECTF--
int(1)
int(2)
