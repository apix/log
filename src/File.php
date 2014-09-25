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

namespace Apix\Log;

use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException as InvalidArgumentException;

/**
 * Minimalist logger implementing PSR-3 relying on PHP's error_log().
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class File extends \Psr\Log\AbstractLogger
{
    const PHP = 0;
    const MAIL = 1;
    const FILE = 3;
    const SAPI = 4;

    /**
     * Holds an associative array of cache adapters.
     * @var array
     */
    // public static $adapters = array(
    //     'MongoClient' => 'Mongo', 'PDO' => 'Pdo',
    //     'ArrayObject' => 'Runtime'
    // );

    /**
     * Holds the destination.
     * @var string
     */
    protected $destination;
    protected $type;
    protected $headers;

    /**
     * @param string $logfile Filename to log messages to (complete path)
     * @throws \InvalidArgumentException When logfile cannot be created or is not writeable
     */
    public function __construct($destination, $type = self::FILE)
    {
        switch($type) {
            // case self::EMAIL:
            //     return error_log(
            //         $message, $type, $this->destination, $this->headers
            //     );

            case self::FILE:
                if (!file_exists($destination) && !touch($destination)) {
                    throw new InvalidArgumentException('Log file ' . $destination . ' cannot be created');
                }
                // if (!is_writable($destination)) {
                //     throw new InvalidArgumentException('Log file ' . $destination . ' is not writeable');
                // }
            break;

            default:
        }

        $this->destination = $destination;
        $this->type = $type;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $const = '\Psr\Log\LogLevel::' . strtoupper($level);
        if( !defined($const) ) {
            throw new InvalidArgumentException(
                sprintf('Invalid log level "%s"', $level)
            );
        }

        // $message = sprintf('[%s] %s %s',
        //                 date('Y-m-d H:i:s'),
        //                 strtoupper($level),
        //                 $this->interpolate($message, $context)
        //             );
        // if(is_array($context)) $context = implode(', ', $context);
        // $handler->setFormatter(new LineFormatter('%level_name% %message%'));

        $message = sprintf('%s %s',
                        $level,
                        $this->interpolate($message, $context)
                    );

        $this->write($message, $this->type);
    }

    public function write($message, $type)
    {
        switch($type) {
            case self::MAIL:
                return error_log(
                    $message, $type, $this->destination, $this->headers
                );

            case self::FILE:
                return error_log($message . PHP_EOL, $type, $this->destination);

            default:
                return error_log($message, $type, $this->destination);
        }
    }

    /**
     * Interpolates context values into the message placeholders.
     * 
     * This function is just copied from the example in the PSR-3 spec
     * build a replacement array with braces around the context keys
     * interpolate replacement values into the message and return
     * It replaces {foo} with the value from $context['foo']
     */
    protected function interpolate($message, array $context = array())
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
            } elseif (is_object($val)) {
                $val = '[object: ' . get_class($val) . ']';
            } else {
                $val = '[type: ' . gettype($val) . ']';
            }
            $replaces['{' . $key . '}'] = $val;
        }

        return strtr($message, $replaces);
    }

}
