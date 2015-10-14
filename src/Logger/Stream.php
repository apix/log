<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log\Logger;

use Psr\Log\InvalidArgumentException;
use Apix\Log\LogEntry;

/**
 * Stream log wrapper.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class Stream extends AbstractLogger implements LoggerInterface
{

    /**
     * Holds the stream.
     * @var resource
     */
    protected $stream;

    /**
     * Constructor.
     *
     * @param  resource|string $stream The stream to append to.
     * @throws InvalidArgumentException If the stream cannot be created/opened.
     */
    public function __construct($stream = 'php://stdout', $mode = 'a')
    {
        if (!is_resource($stream)) {
            $stream = @fopen($stream, $mode);
        }

        if (!is_resource($stream)) {
            throw new InvalidArgumentException(sprintf(
                'The stream "%s" cannot be created or opened', $stream
            ));
        }

        $this->stream = $stream;
    }

    /**
     * {@inheritDoc}
     */
    public function write(LogEntry $log)
    {
        if (!is_resource($this->stream)) {
            throw new \LogicException(
                'The stream resource has been __destruct() too early'
            );
        }
        return (bool) fwrite($this->stream, $log . $log->formatter->separator);
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

}