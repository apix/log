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

use Apix\Log\Logger\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;

/**
 * Minimalist logger implementing PSR-3 relying on PHP's error_log().
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class Logger extends AbstractLogger
{

    /**
     * Holds all the registered loggers.
     * @var Logger\LoggerInterface[] FIFO order.
     */
    protected $loggers = array();

    /**
     * Constructor.
     *
     * @param Logger\LoggerInterface[] $loggers
     */
    public function __construct(array $loggers = array())
    {
        foreach ($loggers as $key => $logger) {
            if ($logger instanceof Logger\LoggerInterface) {
                $this->loggers[] = $logger;
            } else {
                throw new InvalidArgumentException('xx');
            }
        }
    }

    /**
     * Processes the given log.
     * (overwrite abstract)
     *
     * @param  array   $log The record to handle
     * @return boolean False when not processed.
     */
    public function process(array $log)
    {
        $i = $this->getFirstLoggerIndex( $log['code'] );
        if (false !== $i) {
            while (
                isset($this->loggers[$i])
                && $this->loggers[$i]->process( $log )
            ) {
                $i++;
            }

            return true;
        }

        return false;
    }

    /**
     * Checks if any loggers can hanle the given code.
     *
     * @param  integer       $code
     * @return integer|false
     */
    public function getFirstLoggerIndex($code)
    {
        foreach ($this->loggers as $key => $logger) {
            if ( $logger->isHandling( $code ) ) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Gets the name of the PSR-3 logging level.
     *
     * @param  string                   $level
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getPsrLevelName($level)
    {
        $logLevel = '\Psr\Log\LogLevel::' . strtoupper($level);
        if ( !defined($logLevel) ) {
            throw new InvalidArgumentException(
                sprintf('Invalid PSR-3 log level "%s"', $level)
            );
        }

        return $level;
    }

    /**
     * Adds a logger.
     *
     * @param Logger\LoggerInterface $logger
     */
    public function add(Logger\LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

}