<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log;

use Apix\Log\Logger\AbstractLogger;
use Psr\Log\InvalidArgumentException;

/**
 * Minimalist logger implementing PSR-3 relying on PHP's error_log().
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class Logger extends AbstractLogger
{
    /**
     * Holds all the registered loggers as buckets.
     *
     * @var Logger\LoggerInterface[].
     */
    protected $buckets = array();

    /**
     * Constructor.
     *
     * @param Logger\LoggerInterface[] $loggers
     */
    public function __construct(array $loggers = array())
    {
        foreach ($loggers as $key => $logger) {
            if ($logger instanceof Logger\LoggerInterface) {
                $this->buckets[] = $logger;
            } else {
                throw new InvalidArgumentException(
                    sprintf(
                        '"%s" must interface "%s".',
                        get_class($logger),
                        __NAMESPACE__.'\Logger\LoggerInterface'
                    )
                );
            }
        }
        $this->sortBuckets();
    }

    /**
     * Processes the given log.
     * (overwrite abstract).
     *
     * @param LogEntry $log The log record to handle.
     *
     * @return bool False when not processed.
     */
    public function process(LogEntry $log)
    {
        $i = $this->getIndexFirstBucket($log->level_code);
        if (false !== $i) {
            while (
                isset($this->buckets[$i])
                && $this->buckets[$i]->process($log)
            ) {
                ++$i;
            }

            return true;
        }

        return false;
    }

    /**
     * Checks if any log bucket can hanle the given code.
     *
     * @param int $level_code
     *
     * @return int|false
     */
    protected function getIndexFirstBucket($level_code)
    {
        foreach ($this->buckets as $key => $logger) {
            if ($logger->isHandling($level_code)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Gets the name of the PSR-3 logging level.
     *
     * @param string $level_name
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public static function getPsrLevelName($level_name)
    {
        $logLevel = '\Psr\Log\LogLevel::'.strtoupper($level_name);
        if (!defined($logLevel)) {
            throw new InvalidArgumentException(
                sprintf('Invalid PSR-3 log level "%s"', $level_name)
            );
        }

        return $level_name;
    }

    /**
     * Adds a logger.
     *
     * @param Logger\LoggerInterface $logger
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function add(Logger\LoggerInterface $logger)
    {
        $this->buckets[] = $logger;

        return $this->sortBuckets();
    }

    /**
     * Sorts the log buckets, prioritizes top-down by minimal level.
     * Beware: Exisiting level will be in FIFO order.
     * 
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    protected function sortBuckets()
    {
        return usort(
            $this->buckets, function ($a, $b) {
                return $a->getMinLevel() > $b->getMinLevel();
            }
        );
    }

    /**
     * Returns all the registered log buckets.
     *
     * @return array
     */
    public function getBuckets()
    {
        return $this->buckets;
    }
}
