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

namespace Apix;

// Set the default timezone
date_default_timezone_set('UTC');

define('APP_VENDOR', realpath(__DIR__ . '/../vendor'));

// Composer
$loader = require APP_VENDOR . '/autoload.php';
