<?php

use Talisto\Composer\VersionCheck\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

class ConsoleCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->setAutoExit(false);

        // run with cache
        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('path' => __DIR__.'/fixtures'));

        $this->assertStringEqualsFile(__DIR__.'/fixtures/ConsoleFormatterOutputDev.txt', $applicationTester->getDisplay());

        // run without cache
        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('path' => __DIR__.'/fixtures', '--no-cache' => true));

        $this->assertStringEqualsFile(__DIR__.'/fixtures/ConsoleFormatterOutputDev.txt', $applicationTester->getDisplay());

        // run without dev packages
        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('path' => __DIR__.'/fixtures', '--no-dev' => true));

        $this->assertStringEqualsFile(__DIR__.'/fixtures/ConsoleFormatterOutput.txt', $applicationTester->getDisplay());
    }
}