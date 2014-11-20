<?php

use Talisto\Composer\VersionCheck\Checker;
use Talisto\Composer\VersionCheck\Formatter\HTML as Formatter;

class HTMLFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatter()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.json');
        $formatter = new Formatter;
        $result = $formatter->render($checker->checkAll());
        $this->assertStringEqualsFile(__DIR__.'/fixtures/HTMLFormatterOutput.txt', $result);
    }
}