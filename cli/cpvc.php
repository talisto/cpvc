#!/usr/bin/env php
<?php

require_once __DIR__."/../vendor/autoload.php";

use Talisto\Composer\VersionCheck\Console\Application;

$application = new Application;
return $application->run();