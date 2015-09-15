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
 * Logger Interface.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
interface LoggerInterface
{

    /**
     * Write the log.
     *
     * @param  LogEntry $log
     * @return bool
     */
    public function write(LogEntry $log);

}
