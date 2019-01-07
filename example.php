<?php

require __DIR__ . '/vendor/autoload.php';

$error = new \Error\ErrorHandler;

// $error->attach(new \Error\Handler\ConsoleHandler);
// $error->attach(new \Error\Handler\JsonHandler);
$error->attach(new \Error\Handler\WebHandler);
$error->register();

$f = new Symfony\Component\Filesystem\Filesystem();
$f->touch('/usr/lib/foo');
