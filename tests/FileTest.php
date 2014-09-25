<?php
/**
 *
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license     http://opensource.org/licenses/BSD-3-Clause  New BSD License
 *
 */

namespace Apix\Log\tests;

// use Psr\Log\LogLevel;
use Psr\Log\Test\LoggerInterfaceTest;

class FileTest extends LoggerInterfaceTest
{

    protected $file = './apix-unit-test-logger.txt';

    protected function setUp()
    {       
    }

    protected function tearDown()
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     */
    public function testThrowsInvalidArgumentException()
    {
        new \Apix\Log\File('');
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return new \Apix\Log\File($this->file);
    }

    /**
     * This must return the log messages in order with a simple formatting: "<LOG LEVEL> <MESSAGE>"
     *
     * Example ->error('Foo') would yield "error Foo"
     *
     * @return string[]
     */
    public function getLogs()
    {
        return file($this->file, FILE_IGNORE_NEW_LINES);
    }

}
