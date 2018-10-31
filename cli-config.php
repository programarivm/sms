<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__.'/vendor/autoload.php';

$parameters = current(Yaml::parse(file_get_contents(__DIR__.'/app/config/parameters.yml')));
$paths = array(__DIR__.'/src/AppBundle/Entity');
$isDevMode = true;

$dbParams = [
    'host' => $parameters['database_host'],
    'driver' => $parameters['database_driver'],
    'user' => $parameters['database_user'],
    'password' => $parameters['database_password'],
    'dbname' => $parameters['database_name'],
];

$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
$em = \Doctrine\ORM\EntityManager::create($dbParams, $config);

return ConsoleRunner::createHelperSet($em);
