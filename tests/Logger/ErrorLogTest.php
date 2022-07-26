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

class ErrorLogTest extends \PHPUnit\Framework\TestCase
{
    protected $dest = 'test';

    protected function setUp() : void
    {
        // HHVM support
        // @see: https://github.com/facebook/hhvm/issues/3558
        if (defined('HHVM_VERSION')) {
            ini_set('log_errors', 'On');
            ini_set('error_log', $this->dest);
        }

        ini_set('error_log', $this->dest);
    }

    protected function tearDown() : void
    {
        if (file_exists($this->dest)) {
            unlink($this->dest);
        }
    }

    public function testWrite()
    {
        $logger = new Logger\ErrorLog();

        $message = 'test log';
        $logger->debug($message);

        $content = file_get_contents($this->dest);

        $this->assertStringContainsString($message, $content);
    }
}
