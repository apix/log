<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log\Logger;

use Psr\Log\AbstractLogger as PsrAbstractLogger;
use Psr\Log\InvalidArgumentException;
use Apix\Log\LogEntry;
use Apix\Log\LogFormatter;

/**
 * Abstratc class.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
abstract class AbstractLogger extends PsrAbstractLogger
{
    /**
     * The PSR-3 logging levels.
     * @var array
     */
    protected static $levels = array(
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug'
    );

    /**
     * Holds the minimal level index supported by this logger.
     * @var int
     */
    protected $min_level = 7;

    /**
     * Whether this logger will cascade downstream.
     * @var bool
     */
    protected $cascading = true;

    /**
     * Whether this logger will be deferred (push the logs at destruct time).
     * @var bool
     */
    protected $deferred = false;

    /**
     * Holds the deferred logs.
     * @var array
     */
    protected $deferred_logs = array();

    /**
     * Holds the log formatter.
     * @var LogFormatter|null
     */
    protected $log_formatter = null;

    /**
     * Holds the logger options (useful to set default options).
     * @var array
     */
    protected $options = array();

    /**
     * Gets the named level code.
     *
     * @param  string $level_name The name of a PSR-3 level.
     * @return int
     * @throws InvalidArgumentException
     */
    public static function getLevelCode($level_name)
    {
        $level_code = array_search($level_name, static::$levels);
        if (false === $level_code) {
            throw new InvalidArgumentException(
                sprintf('Invalid log level "%s"', $level_name)
            );
        }

        return $level_code;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = array())
    {
        $entry = new LogEntry($level, $message, $context);
        $entry->setFormatter($this->getLogFormatter());
        $this->process($entry);
    }

    /**
     * Processes the given log.
     *
     * @param  LogEntry $log The log entry to process.
     * @return bool Wether this logger cascade downstream.
     */
    public function process(LogEntry $log)
    {
        if ($this->deferred) {
            $this->deferred_logs[] = $log;
        } else {
            $this->write($log);
        }

        return $this->cascading;
    }

    /**
     * Checks whether the given level code is handled by this logger.
     *
     * @param  int $level_code
     * @return bool
     */
    public function isHandling($level_code)
    {
        return $this->min_level >= $level_code;
    }

    /**
     * Sets the minimal level at which this logger will be triggered.
     *
     * @param  string    $name
     * @param  bool|true $cascading Should the logs continue pass that level.
     * @return self
     */
    public function setMinLevel($name, $cascading = true)
    {
        $this->min_level = self::getLevelCode(strtolower($name));
        $this->cascading = (boolean) $cascading;

        return $this;
    }

    /**
     * Alias to self::setMinLevel().
     *
     * @param  string    $name
     * @param  bool|false $blocking Should the logs continue pass that level.
     * @return self
     */
    public function interceptAt($name, $blocking = false)
    {
        return $this->setMinLevel($name, !$blocking);
    }

    /**
     * Returns the minimal level at which this logger will be triggered.
     *
     * @return int
     */
    public function getMinLevel()
    {
        return $this->min_level;
    }

    /**
     * Sets wether to enable/disable cascading.
     *
     * @param  bool $bool
     * @return self
     */
    public function setCascading($bool)
    {
        $this->cascading = (boolean) $bool;

        return $this;
    }

    /**
     * Sets wether to enable/disable log deferring.
     *
     * @param  bool $bool
     * @return self
     */
    public function setDeferred($bool)
    {
        $this->deferred = (boolean) $bool;

        return $this;
    }

    /**
     * Returns all the deferred logs.
     *
     * @return array
     */
    public function getDeferredLogs()
    {
        return $this->deferred_logs;
    }

    /**
     * Process any accumulated deferred log if there are any.
     */
    final public function __destruct()
    {
        if ($this->deferred && !empty($this->deferred_logs)) {

            $messages = array_map(
                function ($log) {
                    return (string) $log;
                },
                $this->deferred_logs
            );
            
            $formatter = $this->getLogFormatter();
            
            $messages = implode($formatter->separator, $messages);

            $entries = new LogEntry('notice', $messages);
            $entries->setFormatter( $formatter );
            $this->write($entries);

            // return $this->formatter->format($this);
        }

        $this->close();
    }

    /**
     * Closes the logger ~ acts as the last resort garbage collector.
     *
     * This method is called last at __destruct() time.
     */
    public function close()
    {
        // empty
    }

    /**
     * Sets a log formatter.
     *
     * @param LogFormatter $formatter
     */
    public function setLogFormatter(LogFormatter $formatter)
    {
        $this->log_formatter = $formatter;
    }

    /**
     * Returns the current log formatter.
     *
     * @return LogFormatter
     */
    public function getLogFormatter()
    {    
        if(!$this->log_formatter) {
            $this->setLogFormatter(new LogFormatter);
        }

        return $this->log_formatter;
    }

    /**
     * Sets and merges the options for this logger, overriding any default.
     *
     * @param array|null $options
     */
    public function setOptions(array $options=null)
    {
        if (null !== $options) {
            $this->options = $options+$this->options;
        }
    }

}