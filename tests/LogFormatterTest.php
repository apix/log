<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log;

class LogFormatterTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;

    protected function setUp()
    {
        $this->logger = new Logger();
    }

    protected function tearDown()
    {
        unset($this->logger);
    }

    public function testGetLogFormatterAsDefault()
    {
        $this->assertInstanceOf(
            '\Apix\Log\LogFormatter',
            $this->logger->getLogFormatter()
        );
    }

    public function testSetLogFormatter()
    {
        $formatter = new AnotherLogFormatter;
        $this->logger->setLogFormatter($formatter);
        $this->assertSame($this->logger->getLogFormatter(), $formatter);
    }
}

class AnotherLogFormatter extends LogFormatter
{
}