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
        $obj = new \stdClass();
        $obj->baz = 'biz';
        $obj->nested = new \stdClass();
        $obj->nested->buz ='bez';

        return array(
            array('bool1', true, '[bool: 1]'),
            array('bool2', false, '[bool: 0]'),
            array('string', 'Foo', 'Foo'),
            array('int', 0, '0'),
            array('float', 0.5, '0.5'),

            array('resource', fopen('php://memory', 'r'), '[type: resource]'),
            array('null', null, ''),

            // objects
            array('obj__toString', new DummyTest(), '__toString!'),
            array('obj_stdClass', new \stdClass(), '{}'),
            array('obj_instance', $obj, '{"baz":"biz","nested":{"buz":"bez"}}'),
            
            // nested arrays...
            array('nested_values', array('foo','bar'), '["foo","bar"]'),
            array('nested_asso', array('foo'=>1,'bar'=>'2'), '{"foo":1,"bar":"2"}'),
            array('nested_object', array(new DummyTest), '[{"foo":"bar"}]'),
            array('nested_unicode', array('ƃol-xᴉdɐ'), '["\u0183ol-x\u1d09d\u0250"]'),
        );
    }

}

class DummyTest
{
    public $foo = 'bar';
    protected $foo2 = 'bar2';
    public function __toString()
    {
        return '__toString!';
    }
}
