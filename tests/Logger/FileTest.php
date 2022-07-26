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
use Psr\Log\InvalidArgumentException;

class FileTest extends \PHPUnit\Framework\TestCase
{
    protected $dest = 'test';

    protected function tearDown() : void
    {
        if (file_exists($this->dest)) {
            unlink($this->dest);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getLogger()
    {
        return new Logger\File($this->dest);
    }

    public function testThrowsInvalidArgumentExceptionWhenFileCannotBeCreated()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Log file "" cannot be created');
        $this->expectExceptionCode(1);
        new Logger\File(null);
    }

    public function testThrowsInvalidArgumentExceptionWhenNotWritable()
    {
        if (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') {
            $file = 'C:\Windows\.';
        } else {
            $file = '.';
        }

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Log file \"{$file}\" is not writable");
        $this->expectExceptionCode(2);


        new Logger\File($file);
    }
}
