<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log\Logger;

use Apix\Log\LogEntry;

/**
 * Minimalist logger implementing PSR-3 relying on PHP's error_log().
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class ErrorLog extends AbstractLogger implements LoggerInterface
{
    const PHP  = 0;
    const MAIL = 1;
    const FILE = 3;
    const SAPI = 4;

    /**
     * Holds the destination string (filename path or email address).
     * @var string
     */
    protected $destination;

    /**
     * Holds the message/delivery type:
     *      0: message is sent to PHP's system logger.
     *      1: message is sent by email to the address in the destination.
     *      3: message is appended to the file destination.
     *      4: message is sent directly to the SAPI.
     * @var integer
     */
    protected $type;

    /**
     * Holds a string of additional (mail) headers.
     * @var string|null
     * @see http://php.net/manual/en/function.mail.php
     */
    protected $headers = null;

    /**
     * Constructor.
     * @param string|null $file The filename to log messages to.
     * @param integer     $type The messag/delivery type.
     */
    public function __construct($file = null, $type = self::PHP)
    {
        $this->destination = $file;
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function write(LogEntry $log)
    {
        $message = (string) $log;

        if(!$this->deferred && $this->type == self::FILE) {
            $message .= $log->formatter->separator;
        }

        return error_log(
            $message,
            $this->type,
            $this->destination,
            $this->headers
        );
    }

}