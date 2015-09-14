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
     * @param  array $log
     * @return bool
     */
    public function write(array $log);

}
