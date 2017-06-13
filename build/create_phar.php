<?php

$phar = new Phar(__DIR__ . "/cpvc.phar", 0, "cpvc.phar");
$phar->buildFromDirectory(realpath(__DIR__.'/../'), '/\.(php|json)$/');
$stub = $phar->createDefaultStub("build/phar_bootstrap.php");
$stub = "#!/usr/bin/php \n".$stub;
$phar->setStub($stub);
$phar->convertToExecutable();
