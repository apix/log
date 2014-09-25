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

use Apix\Log;

class FileTest extends \Psr\Log\Test\LoggerInterfaceTest
{

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return new Log\File('/tmp/logger.txt');
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
        return array('error Foo');
    }



    /**
     * @ expectedException Apix\Cache\PsrCache\InvalidArgumentException
     */
    public function testPoolWithUnsurportedObjectThrowsException()
    {
        $this->logger->info("hello log");
    }

    // public function testPoolFromCacheClientObject()
    // {
    //     $adapter = new \ArrayObject();
    //     $pool = Cache\Factory::getPool($adapter, $this->options);
    //     $this->assertInstanceOf('\Apix\Cache\PsrCache\Pool', $pool);
    //     $this->assertInstanceOf('\Apix\Cache\Runtime', $pool->getCacheAdapter());
    // }

    // /**
    //  * @expectedException Apix\Cache\PsrCache\InvalidArgumentException
    //  */
    // public function testPoolWithUnsurportedStringThrowsException()
    // {
    //     Cache\Factory::getPool('non-existant', $this->options);
    // }

    // public function testPoolFromString()
    // {
    //     $pool = Cache\Factory::getPool('Runtime', $this->options);
    //     $this->assertInstanceOf('\Apix\Cache\PsrCache\Pool', $pool);

    //     $pool = Cache\Factory::getPool('Array', $this->options);
    //     $this->assertInstanceOf('\Apix\Cache\PsrCache\Pool', $pool);
    //     $this->assertInstanceOf('\Apix\Cache\Runtime', $pool->getCacheAdapter());
    // }

    // public function testPoolFromStringMixedCase()
    // {
    //     $pool = Cache\Factory::getPool('arRay', $this->options);
    //     $this->assertInstanceOf('\Apix\Cache\PsrCache\Pool', $pool);
    //     $this->assertInstanceOf('\Apix\Cache\Runtime', $pool->getCacheAdapter());
    // }

    // public function testPoolFromArray()
    // {
    //     $pool = Cache\Factory::getPool(array(), $this->options);
    //     $this->assertInstanceOf('\Apix\Cache\PsrCache\Pool', $pool);
    //     $this->assertInstanceOf('\Apix\Cache\Runtime', $pool->getCacheAdapter());
    // }

    // public function testTaggablePoolFromString()
    // {
    //     $pool = Cache\Factory::getPool('ArrayObject', $this->options, true);
    //     $this->assertInstanceOf('\Apix\Cache\PsrCache\TaggablePool', $pool);
    //     $this->assertInstanceOf('\Apix\Cache\Runtime', $pool->getCacheAdapter());
    // }

    // public function testGetTaggablePool()
    // {
    //     $pool = Cache\Factory::getTaggablePool(array(), $this->options, true);
    //     $this->assertInstanceOf('\Apix\Cache\PsrCache\TaggablePool', $pool);
    //     $this->assertInstanceOf('\Apix\Cache\Runtime', $pool->getCacheAdapter());
    // }

    // /**
    //  * @expectedException \Apix\Cache\Exception
    //  */
    // public function testGetPoolThrowsApixCacheException()
    // {
    //     $adapter = new Cache\Runtime(new \StdClass, $this->options);
    //     Cache\Factory::getPool($adapter);
    // }

}
