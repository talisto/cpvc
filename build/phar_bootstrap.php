<?php

require_once "phar://cpvc.phar/vendor/autoload.php";

use Talisto\Composer\VersionCheck\Console\Application;

$application = new Application;
return $application->run();