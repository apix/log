<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log;

/**
 * Standard log formatter.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class LogFormatter implements LogFormatterInterface
{

    /**
     * Holds this log separator.
     * @var string
     */
    public $separator = PHP_EOL;

    /**
     * Interpolates context values into the message placeholders.
     *
     * Builds a replacement array with braces around the context keys.
     * It replaces {foo} with the value from $context['foo'].
     *
     * @param  string $message
     * @param  array  $context
     * @return string
     */
    public function interpolate($message, array $context = array())
    {        
        $replaces = array();
        foreach ($context as $key => $val) {
            if (is_bool($val)) {
                $val = '[bool: ' . (int) $val . ']';
            } elseif (is_null($val)
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

    /**
     * Formats the given log entry.
     *
     * @param  LogEntry $log The log entry to format.
     * @return string
     */
    public function format(LogEntry $log)
    {
        return sprintf(
            '[%s] %s %s',
            date('Y-m-d H:i:s', $log->timestamp),
            strtoupper($log->name),
            self::interpolate($log->message, $log->context)
        );
    }

}