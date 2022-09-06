<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log;

use Apix\Log\tests\Logger\TestCase;

use Apix\Log\Logger;

class ReadmeTest extends \PHPUnit\Framework\TestCase
{

    /**
     * This must return the log messages in order with a simple formatting: "<LOG LEVEL> <MESSAGE>"
     *
     * Example ->error('Foo') would yield "error Foo"
     *
     * @return string[]
     */
    public function getLogs($logger, $deferred=false)
    {
        $lines = $logger->getItems();

        if($deferred) {
            $lines = explode(
                $logger->getLogFormatter()->separator,
                $lines[0]
            );
        }
        return TestCase::normalizeLogs($lines);
    }

    public function testUsages()
    {
        // Basic usage
        $urgent_logger = new Logger\Runtime;
        $urgent_logger->setMinLevel('critical'); // catch logs >= to `critical`

        $urgent_logger->alert(
            'Running out of {stuff}',
            array('stuff' => 'beers')
        );

        // Advanced usage

        $app_logger = new Logger\Runtime;

        $app_logger->setMinLevel('warning')
            ->setCascading(false)
            ->setDeferred(true);

        // The main logger object (injecting the previous loggers/buckets)
        $logger = new Logger(array($urgent_logger, $app_logger));

        if(true) {
            $debug_logger = new Logger\Runtime;
            $debug_logger->setMinLevel('debug');

            $logger->add($debug_logger);
        }


        // handled by both $urgent_logger & $app_logger
        $e = new \Exception('Boo!');
        $logger->critical(
            'OMG saw {bad-exception}',
            array('bad-exception' => $e)
        );

        // handled by $app_logger
        $logger->error($e); // push an object (or array) directly

        // handled by $debug_logger
        $logger->info('Something happened -> {abc}', array('abc' => array('xyz')));

      
        /* -- All the assertions -- */

        $urgent_logs = $this->getLogs($urgent_logger);

        $this->assertSame(
            'alert Running out of beers',
            $urgent_logs[0]
        );

        $prefixException = version_compare(PHP_VERSION, '7.0.0-dev', '>=')
                ? "Exception: Boo! in "
                : "exception 'Exception' with message 'Boo!' in ";
        
        $this->assertStringStartsWith(
            "critical OMG saw " . $prefixException,
            $urgent_logs[1]
        );
        
        $app_logger->getLogFormatter()->separator = PHP_EOL . '~' . PHP_EOL;
        $app_logger->__destruct(); // just to ensure deferred logs are written

        $app_logs = $this->getLogs($app_logger, true);

        $this->assertStringStartsWith(
            "critical OMG saw " . $prefixException,
            $app_logs[0]
        );

        $this->assertStringStartsWith(
            "error " . $prefixException,
            $app_logs[1]
        );

        $this->assertSame(
            array('info Something happened -> ["xyz"]'),
            $this->getLogs($debug_logger)
        );
    }
}
