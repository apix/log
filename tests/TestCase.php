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

use Psr\Log\Test\LoggerInterfaceTest;

abstract class TestCase extends LoggerInterfaceTest
{

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
            array('null', null, ''),
            array('string', 'Foo', 'Foo'),
            array('int', 0, '0'),
            array('float', 0.5, '0.5'),
            array('__toString', new DummyTest, '__toString...'),
            // array('nested', array('with object' => new DummyTest), '[type: array]'),
            array('object', new \DateTime, '[object: DateTime]'),
            array('resource', fopen('php://memory', 'r'), '[type: resource]'),
            array('stdClass', new \stdClass, '[object: stdClass]')
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