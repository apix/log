<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log\tests\Logger;

use Apix\Log\Logger;

class StreamTest extends TestCase
{
    protected $dest = 'php://memory';
    protected $stream, $logger;

    protected function setUp()
    {
        $this->stream = fopen($this->dest, 'a');
        $this->logger = new Logger\Stream($this->stream);
    }

    protected function tearDown()
    {
        unset($this->logger, $this->stream);
    }

    /**
     * {@inheritDoc}
     */
    public function getLogs()
    {
        fseek($this->stream, 0);
        $lines = fread($this->stream, 1000);
        $lines = explode(
            $this->logger->getLogFormatter()->separator,
            $lines,
            -1
        );
        return self::normalizeLogs($lines);
    }

    /**
     * {@inheritDoc}
     */
    public function getLogger()
    {
        return $this->logger;
    }

    // public function testStreamFromString()
    // {
    //     $logger = new Logger\Stream($this->dest, 'a');
    //     $this->assertEquals($this->logger, $logger);
    // }

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     * @expectedExceptionMessage The stream "" cannot be created or opened
     * @expectedExceptionCode 1
     */
    public function testThrowsInvalidArgumentExceptionWhenFileCannotBeCreated()
    {
        new Logger\Stream(null);
    }

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     * @expectedExceptionMessage Log file "/" is not writable
     * @expectedExceptionCode 2
     */
    // public function testThrowsInvalidArgumentExceptionWhenProtected()
    // {
    //     new Logger\Stream('/');
    // }

}
