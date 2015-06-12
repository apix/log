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
     * @expectedExceptionMessage "stdClass" must interface "Apix\Log\Logger\LoggerInterface"
     */
    public function testConstructorThrowsInvalidArgumentException()
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

    protected function _getMocklogger($r = array())
    {
        return $this->getMock('Apix\Log\Logger\Nil', $r);
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

    protected function _getFilledInLogBuckets($cascading=true)
    {
        // the log bucket for everything (starts at 0 Debug level).
        $dev_log = new Logger\Runtime();
        $dev_log->setMinLevel('debug', $cascading);       

        // The log bucket for Critical, Alert and Emergency.
        $urgent_log = new Logger\Runtime();
        $urgent_log->setMinLevel('critical', $cascading);

        // The log bucket that starts at Notice level 
        $notices_log = new Logger\Runtime();
        $notices_log->setMinLevel('notice', $cascading);

        $this->logger->add($notices_log);
        $this->logger->add($urgent_log);
        $this->logger->add($dev_log);

        // Log some stuff...
        $this->logger->emergency('foo');
        $this->logger->alert('foo');
        $this->logger->critical('foo');

        $this->logger->error('foo');
        $this->logger->warning('foo');
        $this->logger->notice('foo');

        $this->logger->info('foo');
        $this->logger->debug('foo');

        return $this->logger->getBuckets();
    }

    public function testAddLoggersAreAlwaysSortedbyMinimalLevel()
    {
        $buckets = $this->_getFilledInLogBuckets();

        $this->assertCount(3, $buckets);

        $this->assertEquals(5, $buckets[0]->getMinLevel(), 'Critical level');
        $this->assertEquals(2, $buckets[1]->getMinLevel(), 'Notice level');
        $this->assertEquals(0, $buckets[2]->getMinLevel(), 'Debug level');
    }

    public function testLogEntriesAreCascasdingDown()
    {
        $buckets = $this->_getFilledInLogBuckets();

        $this->assertCount(
            3, $buckets[0]->getItems(), 'Entries at Critical minimal level.'
        );
        $this->assertCount(
            6, $buckets[1]->getItems(), 'Entries at Notice minimal level.'
        );
        $this->assertCount(
            8, $buckets[2]->getItems(), 'Entries at Debug minimal level.'
        );
    }

    public function testLogEntriesAreNotCascasding()
    {
        $buckets = $this->_getFilledInLogBuckets(false);

        $this->assertCount(
            3, $buckets[0]->getItems(), 'Entries at Critical minimal level.'
        );
        $this->assertCount(
            3, $buckets[1]->getItems(), 'Entries at Notice minimal level.'
        );
        $this->assertCount(
            2, $buckets[2]->getItems(), 'Entries at Debug minimal level.'
        );
    }

    public function testSetCascading()
    {
        $dev_log = new Logger\Runtime();
        $dev_log->setMinLevel('debug');
        $this->logger->add($dev_log);
        
        $app_log = new Logger\Runtime();
        $app_log->setMinLevel('alert');
        $this->logger->add($app_log);
        
        $buckets = $this->logger->getBuckets();

        $this->logger->alert('test 1');
        $this->assertCount(1, $buckets[0]->getItems());
        $this->assertCount(1, $buckets[1]->getItems());

        $app_log->setCascading(false);
        $this->logger->alert('test 2');

        $this->assertCount(2, $buckets[0]->getItems(), 'app_log should now have 2');
        $this->assertCount(1, $buckets[1]->getItems(), 'dev_log should still have 1');
    }

}