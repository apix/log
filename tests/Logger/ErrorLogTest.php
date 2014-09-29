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

class ErrorLogTest extends TestCase
{

    protected $dest = './apix-unit-test-logger.log';
    // protected $dest = '/dev/stdout';

    protected function setUp()
    {
        ini_set('error_log', $this->dest);
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
        return new Logger\ErrorLog();
    }

}
