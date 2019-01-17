<?php

require __DIR__ . '/vendor/autoload.php';

$error = new \Error\ErrorHandler;
$error->attach(new \Error\Handler\WebHandler(1));
$error->register();

function foo($msg)
{
    try {
        bar($msg);
    } catch (Exception $e) {
        throw new Exception('Fail failed', 0, $e);
    }
}

function bar($msg)
{
    (new Symfony\Component\Filesystem\Filesystem)->touch('/root/'.$msg);
}

foo('Test');
