<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
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

    protected function _getPrivateAttribute($prop)
    {
        return \PHPUnit_Framework_Assert::readAttribute($this->logger, $prop);
    }

    /**
     * @see http://tools.ietf.org/html/rfc5424#section-6.2.1
     */
    public function testGetLevelCodeSameOrderAsRfc5424()
    {
        $this->assertSame(3, Logger::getLevelCode(LogLevel::ERROR));
        $this->assertSame(3, Logger::getLevelCode('error'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage "stdClass" must interface "Apix\Log\Logger\LoggerInterface"
     */
    public function testConstructorThrowsInvalidArgumentException()
    {
        new Logger(array( new \StdClass() ));
    }

    public function testConstructor()
    {
        $err_logger = $this->_getMocklogger(array('process'));
        $err_logger->setMinLevel(LogLevel::ERROR);

        $crit_logger = $this->_getMocklogger(array('process'));
        $crit_logger->setMinLevel(LogLevel::CRITICAL);

        $this->logger = new Logger(array($err_logger, $crit_logger));

        $err_logger->expects($this->once())->method('process');
        $crit_logger->expects($this->once())->method('process');

        $this->logger->error('test err');
        $this->logger->critical('test crit');
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
        $mock_logger->setMinLevel(LogLevel::WARNING);

        $this->logger->warning('test');
    }

    public function testLogWillNotProcess()
    {
        $mock_logger = $this->_getMocklogger(array('process'));
        $mock_logger->setMinLevel(LogLevel::ERROR);

        $mock_logger->expects($this->never()) // <-- process IS NOT expected
            ->method('process');
        $this->logger->add($mock_logger);

        $this->logger->warning('test');
    }

    protected function _getFilledInLogBuckets($cascading=true)
    {
        // The log bucket for everything (starts at 0 Debug level).
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

    /**
     * @see http://tools.ietf.org/html/rfc5424#section-6.2.1
     */
    public function testAddLoggersAreAlwaysSortedbyLevel()
    {
        $buckets = $this->_getFilledInLogBuckets();

        $this->assertCount(3, $buckets);

        $this->assertEquals(2, $buckets[0]->getMinLevel(), 'Critical level');
        $this->assertEquals(5, $buckets[1]->getMinLevel(), 'Notice level');
        $this->assertEquals(7, $buckets[2]->getMinLevel(), 'Debug level');
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
        $this->assertTrue(
            $this->_getPrivateAttribute("cascading"),
            "The 'cascading' propertie should be True by default"
        );
        $this->logger->setCascading(false);
        $this->assertFalse($this->_getPrivateAttribute("cascading"));
    }

    public function testCascading()
    {
        $dev_log = new Logger\Runtime();
        $dev_log->setMinLevel('debug');

        $app_log = new Logger\Runtime();        
        $app_log->setMinLevel('alert');
        
        $this->logger->add($dev_log);
        $this->logger->add($app_log);
        
        $buckets = $this->logger->getBuckets();

        $this->logger->alert('cascading');
        $this->assertCount(1, $buckets[0]->getItems());
        $this->assertCount(1, $buckets[1]->getItems());

        $app_log->setCascading(false)->alert('not-cascading');

        $this->assertCount(2, $buckets[0]->getItems(), 'app_log count = 2');
        $this->assertCount(1, $buckets[1]->getItems(), 'dev_log count = 1');
    }
    
    public function testSetDeferred()
    {
        $this->assertFalse(
            $this->_getPrivateAttribute('deferred'),
            "The 'deferred' propertie should be False by default"
        );
        $this->logger->setDeferred(true);
        $this->assertTrue($this->_getPrivateAttribute('deferred'));
    }

    public function testDeferring()
    {
        $logger = new Logger\Runtime();
        $logger->alert('not-deferred');

        $this->assertCount(1, $logger->getItems());

        $logger->setDeferred(true)->alert('deferred');

        $this->assertCount(1, $logger->getItems());
        $this->assertCount(1, $logger->getDeferredLogs());
    }

    public function testDestructIsNotDeferring()
    {
        $logger = new Logger\Runtime();
        $logger->setDeferred(true)->alert('deferred');
        $logger->setDeferred(false)->alert('not-deferred');

        $logger->__destruct();

        $this->assertCount(1, $logger->getDeferredLogs());
    }

    public function testSeparatorOfLogFormatter()
    {
        $test = $this->logger->getLogFormatter();
        $test->separator = '~';
        
        $this->assertEquals('~', $this->logger->getLogFormatter()->separator);
    }

    public function testInterceptAtAliasSetMinLevel()
    {
        $this->assertEquals(7, $this->logger->getMinLevel());

        $this->logger->setMinLevel('alert', true);
        $this->assertEquals(1, $this->logger->getMinLevel());
        $this->assertTrue($this->_getPrivateAttribute("cascading"));

        $this->logger->interceptAt('warning', true);
        $this->assertEquals(4, $this->logger->getMinLevel());
        $this->assertFalse($this->_getPrivateAttribute("cascading"));
    }

}