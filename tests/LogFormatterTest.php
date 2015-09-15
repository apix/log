<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log;

use Apix\Log\Logger\AbstractLogger;
use Apix\Log\Logger\LoggerInterface;

class StandardOutput extends AbstractLogger implements LoggerInterface
{
    public function write(LogEntry $log)
    {
        echo $log;
    }
}

class MyJsonFormatter extends LogFormatter
{
    public $separator = '~';

    public function format(LogEntry $log)
    {
        // Interpolate the context values into the message placeholders.
        $log->message = self::interpolate($log->message, $log->context);
        
        return json_encode($log);
    }
}

class LogFormatterTest extends \PHPUnit_Framework_TestCase
{
    protected $logger;

    protected function setUp()
    {
        $this->logger = new StandardOutput();
    }

    protected function tearDown()
    {
        unset($this->logger);
    }

    public function testGetLogFormatterReturnsDefaultLogFormatter()
    {
        $this->assertInstanceOf(
            '\Apix\Log\LogFormatter',
            $this->logger->getLogFormatter()
        );
    }

    public function testSetLogFormatter()
    {
        $formatter = new MyJsonFormatter;
        $this->logger->setLogFormatter($formatter);
        $this->assertSame($this->logger->getLogFormatter(), $formatter);
    }

    public function testLogFormatterInterfaceExample()
    {
        $formatter = new MyJsonFormatter;
        $this->logger->setLogFormatter($formatter);
        $this->logger->error('hello {who}', array('who'=>'world'));

        $this->expectOutputRegex(
            '@\{"timestamp":.*\,"name":"error"\,"level_code":4\,"message":"hello world","context":\{"who":"world"\}\,"formatter":\{"separator":"~"\}\}@'
        );
    }
}