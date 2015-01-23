<?php

use Talisto\Composer\VersionCheck\Checker;

class CheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructNonexistantFile()
    {
        $checker = new Checker(__DIR__.'/invalid_file.json');
    }

    /**
     * @expectedException \Seld\JsonLint\ParsingException
     */
    public function testConstructInvalidFile()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.invalid.json');
    }

    public function testConstructValidFile()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.json');
        $this->assertInstanceOf('Talisto\Composer\VersionCheck\Checker', $checker);
        return $checker;
    }

    public function testConstructValidFileMissingDependencies()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.missing.json');
        $this->assertInstanceOf('Talisto\Composer\VersionCheck\Checker', $checker);
        return $checker;
    }

    /**
     * @depends testConstructValidFile
     */
    public function testCheckValid(Checker $checker)
    {
        $result = $checker->checkAll();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('talisto/fake-repository', $result);
        $this->assertInstanceOf('Composer\Package\Package', $result['talisto/fake-repository']['required']);
        $this->assertInstanceOf('Composer\Package\Package', $result['talisto/fake-repository']['latest']);
        $this->assertSame('1.0.1', $result['talisto/fake-repository']['required']->getPrettyVersion());
        $this->assertSame('2.0.0', $result['talisto/fake-repository']['latest']->getPrettyVersion());
    }

    /**
     * @depends testConstructValidFileMissingDependencies
     */
    public function testCheckMissing(Checker $checker)
    {
        $result = $checker->checkAll();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('talisto/fake-repository', $result);
        $this->assertSame(false, $result['talisto/fake-repository']['required']);
        $this->assertSame(false, $result['talisto/fake-repository']['latest']);
    }
}