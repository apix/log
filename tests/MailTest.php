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

class MailTest extends TestCase
{

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
        new \Apix\Log\Mail('');
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return new \Apix\Log\Mail('foo@bar.tld');
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
        // return file($this->file, FILE_IGNORE_NEW_LINES);
    }

}
