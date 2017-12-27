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

    public function testSetAndCheckLogger()
    {
        $this->assertEquals(false, $this->dumper->checkForLogger());
        $this->assertEquals(false, $this->dumper->log('test'));
        $this->dumper->setLogger($this->logger);
        $this->assertEquals($this->logger, $this->dumper->logger);
        $this->assertEquals(true, $this->dumper->checkForLogger());
    }

    public function testInstance()
    {
        $instance = johnykvsky\Utils\JKDumper::instance();
        $this->assertEquals($this->dumper, johnykvsky\Utils\JKDumper::instance());
    }

    public function testStartTime()
    {
        $this->dumper->setLogger($this->logger);
        $startTime = $this->dumper->startTime();
        $this->assertEquals(true, is_float($startTime));
    }

    public function testEndTime()
    {
        $this->dumper->setLogger($this->logger);
        $startTime = $this->dumper->startTime();
        sleep(0.2);
        $endTime = $this->dumper->endTime();
        $this->assertEquals(true, is_float($endTime));
        $this->assertEquals(0, $this->dumper->endTime('missing'));
    }

    public function testGetType()
    {
        $this->assertEquals('stdClass', $this->dumper::getType(new stdClass()));
        $this->assertEquals('null', $this->dumper::getType(null));
        $this->assertEquals('array', $this->dumper::getType(array()));
        $this->assertEquals('string', $this->dumper::getType('test'));
        $this->assertEquals('integer', $this->dumper::getType(12));
        $this->assertEquals('boolean', $this->dumper::getType(true));
        $this->assertEquals('float', $this->dumper::getType(12.50));
    }

    public function testExportVar()
    {
        $this->assertEquals("'test'", $this->dumper::exportVar('test'));
    }

    public function testExport()
    {
        $this->assertEquals("'test'", $this->dumper::export('test', 10, 0, null));
        $this->assertEquals("''", $this->dumper::export('', 10, 0, null));
        $this->assertEquals("1", $this->dumper::export(1, 10, 0, null));
        $this->assertEquals("true", $this->dumper::export(true, 10, 0, null));
        $this->assertEquals("12.50", $this->dumper::export(12.50, 10, 0, null));
        $this->assertEquals("null", $this->dumper::export(null, 10, 0, null));
        $array = "array (1) [\n    'foo' => 'bar'\n]";
        $this->assertEquals($array, $this->dumper::export(array('foo' => 'bar'), 10, 0, null));
        $object = "object(stdClass) {\n    test => true\n}";
        $class = new stdClass();
        $class->test = true;
        $this->assertEquals($object, $this->dumper::export($class, 10, 0, null));
    }

    public function testLog()
    {
        $this->dumper->setLogger($this->logger);
        $this->assertEquals(true, $this->dumper->log('test'));
    }

    public function testVdump()
    {
        $this->assertEquals("'test'", $this->dumper::vdump('test'));
        $this->assertEquals("'test'", $this->dumper::vdump('test', true));
    }

    public function testResource()
    {
        $fileHandle = fopen($this->logger->getLogFilePath(), 'a');
        if (is_resource($fileHandle)) {
            $this->assertEquals('resource', $this->dumper::getType($fileHandle));
            $this->assertEquals(0, strpos($this->dumper::export($fileHandle, 10, 0, null), '(resource)'));
        }
    }

    public function testRecursion()
    {
        $array = array('foo' => 'bar');
        $array[] = array(123, 'test' => &$array);
        $resultArray = "array (2) [\n    'foo' => 'bar',\n    0 => [recursion]\n]";
        $this->assertEquals($resultArray, $this->dumper::vdump($array));
        $class = new stdClass();
        $class->x = &$class;
        $resultClass = "object(stdClass) {\n    x => *RECURSION*\n}";
        $this->assertEquals($resultClass, $this->dumper::vdump($class));
    }

    public function testMaximumDepth()
    {
        $array = array(
            'foo' => array(
                'bar' => array(
                    'baz' => array(
                        'bat' => array(
                            'foo1' => array(
                                'bar1' => array(
                                    'baz1' => array(
                                        'bat1' => array(
                                            'foo2' => array(
                                                'bar2' => array(
                                                    'baz2' => array('end')
                                                )
                                            )
                                        )

                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->assertEquals(492, strpos($this->dumper::vdump($array), '[maximum depth reached]'));
    }
}
