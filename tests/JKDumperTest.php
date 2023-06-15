<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use johnykvsky\Utils\JKDumper;
use johnykvsky\Utils\JKLogger;

class JKDumperTest extends TestCase
{
    public JKDumper $dumper;
    public JKLogger $logger;

    public function testInstance()
    {
        $this->assertEquals($this->dumper, johnykvsky\Utils\JKDumper::instance($this->logger));
    }

    public function testStartTime()
    {
        $startTime = $this->dumper->startTime();
        $this->assertTrue(is_float($startTime));
    }

    public function testEndTime()
    {
        $this->dumper->startTime();
        sleep(1);
        $this->assertTrue(is_float( $this->dumper->endTime()));
    }

    public function testMissingEndtime()
    {
        $this->assertEquals(0, $this->dumper->endTime('missing'));
    }

    public function testVdump()
    {
        $this->assertEquals('string(4) "test"', $this->dumper->vdump('test'));
    }

    public function testLogger()
    {
        $this->assertTrue($this->dumper->loggingEnabled());
    }

    protected function setUp(): void
    {
        $this->logger = new johnykvsky\Utils\JKLogger(__DIR__ . '/logs');
        $this->dumper = new johnykvsky\Utils\JKDumper($this->logger);
    }
}
