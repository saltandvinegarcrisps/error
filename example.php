<?php

ini_set('memory_limit', 1024 * 1024 * 10);
error_reporting(-1);

require __DIR__ . '/vendor/autoload.php';

$error = new \Error\ErrorHandler;

if ('cli' === php_sapi_name()) {
    $error->attach(new \Error\Handler\ConsoleHandler);
} else {
    $error->attach(new \Error\Handler\WebHandler($debug = true));
}

$error->register();

class Project
{
    public static function has($filename): bool
    {
        return is_file('/this/should/fail/'.$filename);
    }

    public static function create($filename)
    {
        (new Symfony\Component\Filesystem\Filesystem)->touch('/this/should/fail/'.$filename);
    }
}

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
    Project::create($filename);
}

function setupProject()
{
    return function ($extra) {
        createProject('readme.md', 'composer.json', 'package.json');
    };
}

setupProject()('test');
