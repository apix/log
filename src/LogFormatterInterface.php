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

/**
 * Log Entry Formatter Interface.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
interface LogFormatterInterface
{

    /**
     * Format the given log entry.
     *
     * @param  LogEntry   $log The log entry to format.
     * @return string
     */
    static public function format(LogEntry $log);

}
