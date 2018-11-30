<?php

$phar = new Phar("cpvc.phar", 0, "cpvc.phar");
$phar->buildFromDirectory(realpath(__DIR__.'/../'), '/\.(php|json)$/');
$stub = $phar->createDefaultStub("build/phar_bootstrap.php");
$stub = "#!/usr/bin/env php\n".$stub;
$phar->setStub($stub);
