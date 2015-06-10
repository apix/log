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

namespace Apix\Log;

use Psr\Log\LogLevel;

class LoggerTest extends \PHPUnit_Framework_TestCase
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

    public function testGetLevelCode()
    {
        $this->assertSame(4, Logger::getLevelCode(LogLevel::ERROR));
        $this->assertSame(4, Logger::getLevelCode('error'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorThrowsException()
    {
        new Logger( array( new \StdClass() ) );
    }

    public function testConstructor()
    {
        $e = $this->_getMocklogger(array('process'));
        $e->setMinLevel( LogLevel::ERROR );

        $e->expects($this->once())->method('process');

        $c = $this->_getMocklogger(array('process'));
        $c->setMinLevel( LogLevel::CRITICAL );

        $c->expects($this->once())->method('process');

        $this->logger = new Logger( array($c, $e) );

        $this->logger->error('test');
        $this->logger->critical('test');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetLevelCodeThrows()
    {
        Logger::getLevelCode('non-existant');
    }

    public function testgGtPsrLevelName()
    {
        $this->assertEquals('error', Logger::getPsrLevelName(LogLevel::ERROR));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetPsrLevelNameWillThrows()
    {
        Logger::getPsrLevelName('non-existant');
    }

    public function _getMocklogger($r = array())
    {
        return $this->getMock('Apix\Log\Logger\Null', $r);
    }

    public function testWriteIsCalled()
    {
        $mock_logger = $this->_getMocklogger(array('write'));
        $mock_logger->expects($this->once())
                ->method('write');

        $this->logger->add($mock_logger);

        $this->logger->info('test');
    }

    public function testLogWillProcess()
    {
        $mock_logger = $this->_getMocklogger(array('process'));
        $mock_logger->expects($this->once()) // <-- process IS expected
                ->method('process');

        $this->logger->add($mock_logger);
        $mock_logger->setMinLevel( LogLevel::WARNING );

        $this->logger->warning('test');
    }

    public function testLogWillNotProcess()
    {
        $mock_logger = $this->_getMocklogger(array('process'));
        $mock_logger->setMinLevel( LogLevel::ERROR );

        $mock_logger->expects($this->never()) // <-- process IS NOT expected
                ->method('process');
        $this->logger->add($mock_logger);

        $this->logger->warning('test');
    }

    public function TODO_testFunctional()
    {
        // $this->expectOutputString('foo');

        // $l1 = new Logger\Sapi('/tmp/functional-test.log');

        $l1 = new Logger\File('/tmp/functional-test.log');
        // $l1->isCascading(true);

        $l2 = new Logger\Mail('d@d.c');
        // $l2 = new Logger\Sapi('');

        $l2->setMinLevel( LogLevel::ALERT );
        $l2->isCascading(true);

        $this->logger->add($l2);
        $this->logger->add($l1);

        $this->logger->debug('test filed logged');
        $this->logger->alert('test by email {bb}', array('bb'=>123));
    }

    /**
     * @group todo
     */
    public function testFunctionalExample()
    {
        $logger = new Logger();

        // the log bucket for critical, alert and emergency
        $mail_log = new Logger\Mail('foo@bar.boo');
        $mail_log->setMinLevel('critical');
        $this->logger->add($mail_log);

        // the log bucket for notice, warning and error
        $prod_log = new Logger\File('/tmp/apix_prod.log');
        $prod_log->setMinLevel('notice');
        $this->logger->add($prod_log);

        if (true) {
          // the log bucket for info and debug
          $dev_log = new Logger\File('/tmp/apix_dev.log');
          $this->logger->add($dev_log);
        }

        $this->logger->debug('test filed logged');
        $this->logger->alert('test by email {bb}', array('bb'=>123));

    }

}
