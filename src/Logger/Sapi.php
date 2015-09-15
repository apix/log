<?php

/**
 * This file is part of the Apix Project.
 *
 * (c) Franck Cassedanne <franck at ouarz.net>
 *
 * @license http://opensource.org/licenses/BSD-3-Clause  New BSD License
 */

namespace Apix\Log\Logger;

/**
 * Sapi (Server API) log wrapper.
 *
 * @author             Franck Cassedanne <franck at ouarz.net>
 * @codeCoverageIgnore
 */
class Sapi extends ErrorLog
{

    /**
     * Constructor.
     *
     * @param string $destination
     */
    public function __construct($destination = \PHP_SAPI)
    {
        $this->destination = $destination;
        $this->type = static::SAPI;
    }

}
