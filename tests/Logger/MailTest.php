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

class MailTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     * @expectedExceptionMessage "" is an invalid email address
     */
    public function testThrowsInvalidArgumentExceptionWhenNull()
    {
        new Logger\Mail(null);
    }

    /**
     * @expectedException Psr\Log\InvalidArgumentException
     * @expectedExceptionMessage "foo" is an invalid email address
     */
    public function testThrowsInvalidArgumentException()
    {
        new Logger\Mail('foo');
    }

    public function testConstructor()
    {
        new Logger\Mail('foo@bar.com', 'CC: some@somewhere.com');
    }

}
