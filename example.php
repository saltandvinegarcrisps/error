<?php

require __DIR__ . '/vendor/autoload.php';

$error = new \Error\ErrorHandler;

if (\Error\Handler\ConsoleHandler::isConsole()) {
    $error->attach(new \Error\Handler\ConsoleHandler);
}

if (\Error\Handler\JsonHandler::isJson()) {
    $error->attach(new \Error\Handler\JsonHandler);
}

if (\Error\Handler\WebHandler::isWeb()) {
    $error->attach(new \Error\Handler\WebHandler);
}

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
