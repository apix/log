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

use Psr\Log\AbstractLogger as AbsPsrLogger;
use Psr\Log\InvalidArgumentException;

/**
 * Abstratc class.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
abstract class AbstractLogger extends AbsPsrLogger
{
    /**
     * The PSR-3 logging levels.
     * @var array
     */
    static protected $levels = array(
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency'
    );

    /**
     * Holds the minimal level index supported by this logger.
     * @var integer
     */
    protected $min_level = 0;

    /**
     * Whether this logger allows log to cascade downstream.
     * @var bool
     */
    protected $cascading = true;

    /**
     * Gets the named level code.
     *
     * @return string                   $name
     * @throws InvalidArgumentException
     */
    public static function getLevelCode($name)
    {
        $code = array_search($name, static::$levels);
        if (false === $code) {
            throw new InvalidArgumentException(
                sprintf('Invalid log level "%s"', $name)
            );
        }

        return $code;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = array())
    {
        $log = array(
            'name' => $level,
            'code' => static::getLevelCode($level),
            'msg'  => (string) $message,
            'ctx'  => $context
        );

        $this->process($log);
    }

    /**
     * Processes the given log.
     *
     * @param  array   $log The record to handle
     * @return Boolean true means that this handler handled the record, and that bubbling is not permitted.
     *                     false means the record was either not processed or that this handler allows bubbling.
     */
    public function process(array $log)
    {
        $log['msg'] = sprintf('[%s] %s %s',
                        date('Y-m-d H:i:s'),
                        strtoupper($log['name']),
                        $this->interpolate($log['msg'], $log['ctx'])
        );

        // return
        $this->write($log); // &&

        return $this->cascading;
    }

    /**
     * Checks whether the given level code is handled by this handler.
     *
     * @param  integer $level_code
     * @return boolean
     */
    public function isHandling($level_code)
    {
        // return $this->level <= $level_code;
        return $level_code >= $this->min_level;
    }

    /**
     * Sets the minimal level at which this handler will be triggered.
     *
     * @param  string $name
     * @return self
     */
    public function setMinLevel($name)
    {
        $this->min_level = (int) static::getLevelCode($name);

        return $this;
    }

    /*
     * Sets the minimal level at which this handler will be triggered.
     *
     * @param  bool $bool
     * @return self
     */
    public function isCascading($bool)
    {
        $this->cascading = (boolean) $bool;

        return $this;
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * This function is just copied from the example in the PSR-3 spec
     * build a replacement array with braces around the context keys
     * interpolate replacement values into the message and return
     * It replaces {foo} with the value from $context['foo']
     */
    public static function interpolate($message, array $context = array())
    {
        $replaces = array();
        foreach ($context as $key => $val) {
            if (is_bool($val)) {
                $val = '[bool: ' . (int) $val . ']';
            } elseif (
                is_null($val)
                || is_scalar($val)
                || ( is_object($val) && method_exists($val, '__toString') )
            ) {
                $val = (string) $val;
            } elseif (is_array($val) || is_object($val)) {
                $val = @json_encode($val);
            } else {
                $val = '[type: ' . gettype($val) . ']';
            }
            $replaces['{' . $key . '}'] = $val;
        }

        return strtr($message, $replaces);
    }

}
