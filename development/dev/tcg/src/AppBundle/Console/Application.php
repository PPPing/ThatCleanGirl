<?php
// application.php

require __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../../app/bootstrap.php.cache';
require_once __DIR__.'/../../../app/AppKernel.php';

use Symfony\Component\ClassLoader\ApcClassLoader;
use AppBundle\Console\Command\ServiceCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;

$kernel = new AppKernel('prod',false);
$kernel->loadClassCache();

//$kernel = new AppKernel('dev', true);
$application = new Application($kernel);
$application->add(new ServiceCommand());
$application->run();

