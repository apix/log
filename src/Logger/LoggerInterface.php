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
 * Logger Interface providing PSR-3 (PSR Log) compliency.
 *
 * To contribute a logger, essentially it needs to:
 *    1.) Extends the `AbstractLogger`
 *    2.) Implements this interface `LoggerInterface`
 *    3.) Cast to string the provided `LogEntry $log` e.g. (string) $log
 *
 * @example 
 *   class StandardOutput extends AbstractLogger implements LoggerInterface
 *   {
 *     public function write(LogEntry $log)
 *     {
 *         echo $log;
 *     }
 *   }
 *
 * @see tests/InterfacesTest.php     For a more detailed example.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
interface LoggerInterface
{

    /**
     * Writes the given log entry.
     *
     * @param  LogEntry $log
     * @return bool Wether the log entry was successfully written or not.
     */
    public function write(LogEntry $log);

}
