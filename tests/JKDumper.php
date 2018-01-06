<?php

use PHPUnit\Framework\TestCase;

class JKDumperTest extends TestCase
{
    public $dumper;
    public $logger;

    protected function setUp()
    {
        $this->dumper = new johnykvsky\Utils\JKDumper();
        $this->logger = new Katzgrau\KLogger\Logger(__DIR__.'/files');
    }

    public function testSapi()
    {
        $this->assertEquals(PHP_SAPI, $this->dumper->getSapi());
    }

    public function testInstance()
    {
        $instance = johnykvsky\Utils\JKDumper::instance();
        $this->assertEquals($this->dumper, johnykvsky\Utils\JKDumper::instance());
    }

    public function testStartTime()
    {
        $startTime = $this->dumper->startTime();
        $this->assertEquals(true, is_float($startTime));
    }

    public function testEndTime()
    {
        $startTime = $this->dumper->startTime();
        sleep(0.2);
        $endTime = $this->dumper->endTime();
        $this->assertEquals(true, is_float($endTime));
    }

    public function testMissingEndtime()
    {
        $this->assertEquals(0, $this->dumper->endTime('missing'));
    }

    public function testVdump()
    {
        $this->assertEquals('string(4) "test"', $this->dumper::vdump('test'));
    }

    public function testSetLogger()
    {
        $this->assertEquals(false, $this->dumper->log('test'));
        $this->dumper->setLogger($this->logger);
        $this->assertEquals($this->logger, $this->dumper->logger);
    }

    public function testCheckForLogger()
    {
        $this->assertEquals(false, $this->dumper->checkForLogger());
        $this->dumper->setLogger($this->logger);
        $this->assertEquals(true, $this->dumper->checkForLogger());
    }

    public function testLog()
    {
        $this->dumper->setLogger($this->logger);
        $this->assertEquals(true, $this->dumper->log('test'));
    }
}
