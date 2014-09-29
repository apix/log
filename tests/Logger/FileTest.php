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

namespace Apix\Log\tests\Logger;

use Apix\Log\Logger;

class FileTest extends TestCase
{

    protected $dest = './apix-unit-test-logger.log';

    protected function setUp()
    {
    }

    protected function tearDown()
    {
        if (file_exists($this->dest)) {
            unlink($this->dest);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getLogger()
    {
        return new Logger\File($this->dest);
    }

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     * @expectedExceptionMessage Log file "" cannot be created
     * @expectedExceptionCode 1
     */
    public function testThrowsInvalidArgumentExceptionWhenCannotCreate()
    {
        new Logger\File(null);
    }

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     * @expectedExceptionMessage Log file "/" is not writeable
     * @expectedExceptionCode 2
     */
    public function testThrowsInvalidArgumentExceptionWhenProtected()
    {
        new Logger\File('/');
    }

}
