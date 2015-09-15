<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log;

use Psr\Log\InvalidArgumentException;

/**
 * Describes a log Entry.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class LogEntry
{

    /**
     * Holds this log timestamp.
     * @var integer
     */
    public $timestamp;

    /**
     * Holds this log name.
     * @var string
     */
    public $name;

    /**
     * Holds this log level code.
     * @var integer
     */
    public $level_code;

    /**
     * Holds this log message.
     * @var string
     */
    public $message;

    /**
     * Holds this log context.
     * @var array
     */
    public $context;

    /**
     * Holds this log formatter.
     * @var LogFormatter
     */
    public $formatter;

    /**
     * Constructor.
     *
     * @param string $name    The level name.
     * @param string $message The message for this log entry.
     * @param array  $context The contexts for this log entry.
     */
    public function __construct($name, $message, array $context = array())
    {
        $this->timestamp = time();

        $this->name = $name;
        $this->level_code = Logger::getLevelCode($name);

        // Message is not a string let assume it is a context -- and permute. 
        if (!is_string($message)) {
            $context = array('ctx' => $message);
            $message = '{ctx}';
        }
        $this->message = $message;
        $this->context = $context;
    }
    
    public function setFormatter(LogFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Returns the formated string for this log entry.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->formatter->format($this);
    }

}