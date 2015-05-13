<?php
// application.php

require __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../../app/AppKernel.php';

use AppBundle\Console\Command\ServiceCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;

$kernel = new AppKernel('dev', true);
$application = new Application($kernel);
$application->add(new ServiceCommand());
$application->run();

