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

// todo
    public function _testExample()
    {
        $g = $this->_getMocklogger( array('process') );
        $g->expects($this->once())->method('process');

        $a = $this->_getMocklogger( array('process') );
        $a->setMinLevel( LogLevel::ALERT );
        $a->expects($this->once())->method('process');

        $this->logger->add($g);
        $this->logger->add($a);

        $this->logger->alert('test');
        $this->logger->debug('test');

    }

    /**
     * @group test
     */
    public function testFunctional()
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

    public function _testHandlersNotCalledBeforeFirstHandling()
    {
        $l1 = $this->_getMocklogger();
        $l1->expects($this->never())
            ->method('isHandling')
            ->will($this->returnValue(false));

        $l1->expects($this->once())
            ->method('process')
            ->will($this->returnValue(false));

        $this->logger->add($l1);

        $l2 = $this->_getMocklogger();
        $l2->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(true));

        $l2->expects($this->once())
            ->method('process')
            ->will($this->returnValue(false));

        $this->logger->add($l2);

        $l3 = $this->_getMocklogger();
        $l3->expects($this->once())
            ->method('isHandling')
            ->will($this->returnValue(false))
        ;
        $l3->expects($this->never())
            ->method('process')
        ;
        $this->logger->add($l3);

        $this->logger->debug('test');
    }

    public function _testGetFirstLoggerIndex()
    {
        $l1 = $this->_getMocklogger();
        $l1->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(false));
        $this->logger->add($l1);

        $this->assertFalse($this->logger->getFirstLoggerIndex(LogLevel::DEBUG));

        $l2 = $this->_getMocklogger();
        $l2->setMinLevel( LogLevel::ALERT );
        $l2->expects($this->any())
            ->method('isHandling')
            ->will($this->returnValue(true));
        $this->logger->add($l2);

        $this->assertSame(0, $this->logger->getFirstLoggerIndex(LogLevel::DEBUG));
    }

}
