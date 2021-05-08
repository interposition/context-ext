<?php declare(strict_types=1);

spl_autoload_register(function (string $class) {
    $class = str_replace('Interposition\Context\Test\\', '', $class);

    require __DIR__.'/'.$class.'.php';
});
