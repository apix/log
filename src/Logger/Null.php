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
 * Null log wrapper.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 * @codeCoverageIgnore
 */
class Null extends AbstractLogger implements LoggerInterface
{

    /**
     * {@inheritDoc}
     */
    public function write(array $log)
    {
        return false;
    }

}
