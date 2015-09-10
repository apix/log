APIx Log, very thin PSR-3 logger
================================
[![Latest Stable Version](https://poser.pugx.org/apix/log/v/stable.svg)](https://packagist.org/packages/apix/log)  [![Build Status](https://travis-ci.org/frqnck/apix-log.png?branch=master)](https://travis-ci.org/frqnck/apix-log)  [![Code Quality](https://scrutinizer-ci.com/g/frqnck/apix-log/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/frqnck/apix-log/?branch=master)  [![Code Coverage](https://scrutinizer-ci.com/g/frqnck/apix-log/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/frqnck/apix-log/?branch=master)  [![License](https://poser.pugx.org/apix/log/license.svg)](https://packagist.org/packages/apix/log)

Minimalist **PSR-3** compliant logger.

* Unit **tested** and compliant with PSR0, PSR1 and PSR2.
* Continuously integrated against **PHP 5.3**, **5.4**, **5.5**, **5.6**, **7.0** and **HHVM**.
* Available as a **[Composer](https://packagist.org/packages/apix/log)** ~~and as a [PEAR](http://pear.ouarz.net)~~ package.

Feel free to comment, send pull requests and patches...

:new: *Log dispatch can be postponed/accumulated using `setDeferred()`.*

Basic usage (*standalone*)
-----------
```php
use Apix\Log;

$urgent_logger = new Logger\Mail('franck@foo.bar');
$urgent_logger->setMinLevel('critical');   // catch logs >= to `critical`
```

This logger/bucket will intercept `critical`, `alert` and `emergency` logs (see [Log levels](#Log-levels)).

To log an event, use:

```php
$urgent_logger->alert('Running out of {stuff}', ['stuff' => 'beers']);
```

Advanced usage (*multi-logs dispatcher*)
--------------
Okay. Lets create some additional loggers/buckets -- one generic, another one for development.

```php
$app_logger = new Logger\File('/var/log/apix_app.log');
$app_logger->setMinLevel('notice')  // intercept logs that are >= `notice`,
           ->setDeferred(True)      // postpone/accumulate logs processing,
           ->setCascading(False);   // don't propagate to further buckets.

// The main logger object (injecting the previous buckets)
$logger = new Logger( array($urgent_logger, $app_logger) );

if(DEBUG) {
  // Bucket for the remaining logs -- i.e. `info` and `debug`
  $debug_logger = new Logger\File('/tmp/apix_develop.log');
  $debug_logger->setMinLevel('debug');  // Note: `debug` is the default!

  $logger->add($debug_logger);   // another way to inject a bucket
}
```

Note that `setCascading()` was set to False (default is True) which means that any intercepted log entries won't continue downstream past that particular bucket. So in that case, the debug bucket will only get `info` and `debug` log entries.

Finally, you can push some log entries in the following manner:

```php
$logger->notice('Something happened -> {ctx}', array('ctx' => array(...) ) );
  
$e = New \Exception('boo!');
$logger->critical('OMG saw {exception}', [ 'exception' => $e ]);

$logger->debug($e);     // or push an object or an array directly
```

Log levels
----------
The eight [RFC 5424][] levels of logs are supported, in order:

Severity  | Description
----------|------------
emergency | System level failure (not application level)
alert     | Failure that requires immediate attention
critical  | Serious failure at the application level
error     | Runtime errors, used to log unhandled exceptions
warning   | May indicate that an error will occur if action is not taken
notice    | Events that are unusual but not error conditions
info      | Normal operational messages (no action required)
debug     | Verbose info useful to developers for debugging purposes (default)

[PSR-3]: http://tools.ietf.org/html/rfc5424
[RFC 5424]: http://tools.ietf.org/html/rfc5424

Installation
------------------------

Install the current major version using Composer with (recommended)
```
$ composer require apix/log:1.1.*
```
Or install the latest stable version with
```
$ composer require apix/log
```

License
-------
APIx Log is licensed under the New BSD license -- see the `LICENSE.txt` for the full license details.
