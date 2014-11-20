<?php

use Talisto\Composer\VersionCheck\Checker;

class CheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructInvalid()
    {
        $checker = new Checker(__DIR__.'/invalid_file.json');
    }

    public function testConstructValid()
    {
        $checker = new Checker(__DIR__.'/fixtures/composer.json');
        $this->assertInstanceOf('Talisto\Composer\VersionCheck\Checker', $checker);
        return $checker;
    }

    /**
     * @depends testConstructValid
     */
    public function testCheck(Checker $checker)
    {
        $result = $checker->checkAll();
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('talisto/fake-repository', $result);
        $this->assertInstanceOf('Composer\Package\Package', $result['talisto/fake-repository']['current']);
        $this->assertInstanceOf('Composer\Package\Package', $result['talisto/fake-repository']['latest']);
        $this->assertSame('1.0.1', $result['talisto/fake-repository']['current']->getPrettyVersion());
        $this->assertSame('2.0.0', $result['talisto/fake-repository']['latest']->getPrettyVersion());
    }
}