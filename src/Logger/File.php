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

namespace Apix\Log\Logger;

use Psr\Log\InvalidArgumentException;

/**
 * Minimalist file basd logger implementing PSR-3 relying on PHP's error_log().
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class File extends ErrorLog implements LoggerInterface
{

    /**
     * Constructor.
     *
     * @param  string                   $file The file to append to.
     * @throws InvalidArgumentException If the file is not writeable.
     */
    public function __construct($file)
    {
        if (!file_exists($file) && !touch($file)) {
            throw new InvalidArgumentException(
                sprintf('Log file "%s" cannot be created', $file), 1
            );
        }
        if (!is_writable($file)) {
            throw new InvalidArgumentException(
                sprintf('Log file "%s" is not writeable', $file), 2
            );
        }

        $this->destination = $file;
        $this->type = static::FILE;
    }

}
