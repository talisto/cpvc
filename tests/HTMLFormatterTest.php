<?php

use Talisto\Composer\VersionCheck\Checker;
use Talisto\Composer\VersionCheck\Formatter\HTML as Formatter;

class HTMLFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatterValid()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.json');
        $formatter = new Formatter;
        $result = $formatter->render($checker->checkAll());
        $this->assertStringEqualsFile(__DIR__.'/fixtures/HTMLFormatterOutput.txt', $result);
    }

    public function testFormatterMissing()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.missing.json');
        $formatter = new Formatter;
        $result = $formatter->render($checker->checkAll());
        $this->assertStringEqualsFile(__DIR__.'/fixtures/HTMLFormatterOutputMissing.txt', $result);
    }
}