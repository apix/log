<?php

namespace Apix\Log\Logger;

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
