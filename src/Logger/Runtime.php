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
 * Runtime (Array/ArrayObject) log wrapper.
 *
 * @author Franck Cassedanne <franck at ouarz.net>
 */
class Runtime extends AbstractLogger implements LoggerInterface
{
    /**
     * Holds the logged items.
     * @var array
     */
    protected $items = array();

    /**
     * {@inheritDoc}
     */
    public function write(array $log)
    {
        $this->items[] = $log['msg'];
    }

    /**
     * Returns the logged items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

}
