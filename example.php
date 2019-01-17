<?php

require __DIR__ . '/vendor/autoload.php';

$error = new \Error\ErrorHandler;
$error->attach(new \Error\Handler\WebHandler(1));
$error->register();

function createProject(...$files)
{
    foreach ($files as $filename) {
        try {
            createFile($filename);
        } catch (Exception $e) {
            throw new Exception('Failed', $e->getCode(), $e);
        }
    }
}

function createFile($filename)
{
    (new Symfony\Component\Filesystem\Filesystem)->touch('/this/should/fail/'.$filename);
}

function setupProject()
{
    createProject('readme.md', 'composer.json', 'package.json');
}

setupProject();
