<?php

require __DIR__ . '/vendor/autoload.php';

$error = new \Error\ErrorHandler;
$error->attach(new \Error\Handler\WebHandler(1));
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
