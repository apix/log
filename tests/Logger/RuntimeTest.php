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

class RuntimeTest extends TestCase
{
    protected $logger;

    protected function setUp()
    {
        $this->logger = new Logger\Runtime();
    }

    protected function tearDown()
    {
        unset($this->logger);
    }

    /**
     * {@inheritDoc}
     */
    public function getLogs()
    {
        return $this->_normalizeLogs($this->logger->getItems());
    }

    /**
     * {@inheritDoc}
     */
    public function getLogger()
    {
        return $this->logger;
    }

    public function testAbstractLogger()
    {
        $context = array('foo', 'bar');
        $this->logger->debug('msg1', $context);
        $this->logger->error('msg2', $context);

        $logs = $this->logger->getItems();

        $this->assertSame(2, count($logs));
        $this->assertSame(array('debug msg1', 'error msg2'), $this->_normalizeLogs($logs));
    }

}
