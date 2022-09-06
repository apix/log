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
use Psr\Log\InvalidArgumentException;

class StreamTest extends \PHPUnit\Framework\TestCase
{
    protected $dest = 'php://memory';
    protected $stream, $logger;

    protected function setUp() : void
    {
        $this->stream = fopen($this->dest, 'a');
        $this->logger = new Logger\Stream($this->stream);
    }

    protected function tearDown() : void
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

    public function testThrowsInvalidArgumentExceptionWhenFileCannotBeCreated()
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage('Path cannot be empty');
        new Logger\Stream(null);
    }

    public function testThrowsLogicException()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The stream resource has been __destruct() too early');

        $logger = new Logger\Stream;
        $logger->__destruct();
        $logger->debug('foo');
    }
}
