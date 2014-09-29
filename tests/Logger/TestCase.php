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

use Psr\Log\Test\LoggerInterfaceTest;

abstract class TestCase extends LoggerInterfaceTest
{

    protected function _normalizeLogs($logs)
    {
        $normalize = function ($log) {
            return preg_replace_callback(
                '{^\[.+\] (\w+) (.+)?}',
                function ($match) {
                    return strtolower($match[1]) . ' '
                    . (
                        isset($match[2]) ? $match[2] : null
                    );
                },
                $log);
        };

        return array_map($normalize, $logs);
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
        return $this->_normalizeLogs(file($this->dest, FILE_IGNORE_NEW_LINES));
    }

    /**
     * @dataProvider providerContextes
     */
    public function testContextOutput($key, $context, $exp)
    {
        $this->getLogger()->debug('{'.$key.'}', array( $key => $context));

        $this->assertEquals(array('debug ' . $exp), $this->getLogs());
    }

    public function providerContextes()
    {
        return array(
            array('bool1', true, '[bool: 1]'),
            array('bool2', false, '[bool: 0]'),
            array('string', 'Foo', 'Foo'),
            array('int', 0, '0'),
            array('float', 0.5, '0.5'),
            array('__toString', new DummyTest(), '__toString...'),
            // array('nested', array('with object' => new DummyTest), '[type: array]'),
            array('object', new \DateTime(), '[object: DateTime]'),
            array('resource', fopen('php://memory', 'r'), '[type: resource]'),
            array('stdClass', new \stdClass(), '[object: stdClass]'),
            array('null', null, '')
        );
    }

}

class DummyTest
{
    public function __toString()
    {
        return '__toString...';
    }
}
