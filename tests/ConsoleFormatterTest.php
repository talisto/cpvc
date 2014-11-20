<?php

use Talisto\Composer\VersionCheck\Checker;
use Talisto\Composer\VersionCheck\Formatter\Console as Formatter;
use Symfony\Component\Console\Output\BufferedOutput;

class ConsoleFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatter()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.json');
        $output = new BufferedOutput;
        $formatter = new Formatter($output);
        $formatter->render($checker->checkAll());
        $result = $output->fetch();
        $this->assertStringEqualsFile(__DIR__.'/fixtures/ConsoleFormatterOutputDev.txt', $result);
    }
}