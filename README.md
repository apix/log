APIx Log, very thin PSR-3 logger
================================
[![Latest Stable Version](https://poser.pugx.org/apix/log/v/stable.svg)](https://packagist.org/packages/apix/log)  [![Build Status](https://travis-ci.org/frqnck/apix-log.png?branch=master)](https://travis-ci.org/frqnck/apix-log)  [![Code Quality](https://scrutinizer-ci.com/g/frqnck/apix-log/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/frqnck/apix-log/?branch=master)  [![Code Coverage](https://scrutinizer-ci.com/g/frqnck/apix-log/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/frqnck/apix-log/?branch=master)  [![License](https://poser.pugx.org/apix/log/license.svg)](https://packagist.org/packages/apix/log)

Minimalist and fast **PSR-3** compliant logger.

* Light, come out-of-the-box bundle with wrappers for:
 * [ErrorLog](src/Logger/ErrorLog.php), [File](src/Logger/File.php), [Mail](src/Logger/Mail.php), [Sapi](src/Logger/Sapi.php) ~ built around the `error_log()` function,
 * [Runtime](src/Logger/Runtime.php) ~ as an Array/ArrayObject wrapper, and [Nil](src/Logger/Nil.php) ~ as Null wrapper,
 * [Stream](src/Logger/Stream.php) ~ logs are sent to sockets, local and remote files and other similar resources (default to screen without output buffer),
* Extendable, additional logging backends are available:
 * [PHPMailer/apix-log-phpmailer](/PHPMailer/apix-log-phpmailer) ~ logs are sent using PHPMailer.
 * [jspalink/apix-log-pushover](/jspalink/apix-log-pushover) ~ logs are sent using Pushover.
 * More contributions will be linked here.
* Clean API, see the [`LoggerInterface`](src/Logger/LoggerInterface.php) and the [`LogFormatterInterface`](src/LogFormatterInterface.php).
* 100% Unit **tested** and compliant with PSR0, PSR1 and PSR2.
* Continuously integrated against **PHP 5.3**, **5.4**, **5.5**, **5.6**, **7.0** and **HHVM**.
* Available as a [Composer](https://packagist.org/packages/apix/log) ~~and as a [PEAR](http://pear.ouarz.net)~~ package.

Feel free to comment, send pull requests and patches...

:new: *Log dispatch can be postponed/accumulated using `setDeferred()`.*

Basic usage ~ *standalone*
-----------
```php
use Apix\Log;

$urgent_logger = new Logger\Mail('franck@foo.bar');
$urgent_logger->setMinLevel('critical');   // catch logs >= to `critical`
```

This simple logger is now set to intercept `critical`, `alert` and `emergency` logs.

To log an event, use:

```php
$urgent_logger->alert('Running out of {stuff}', ['stuff' => 'beers']);
```

Advanced usage ~ *multi-logs dispatcher* (bucket of logs)
--------------
Lets create an additional logger.
```php
$app_logger = new Logger\File('/var/log/apix_app.log');
$app_logger->setMinLevel('warning')  // intercept logs that are >= `warning`
           ->setCascading(false)     // don't propagate to further buckets
           ->setDeferred(true);      // postpone/accumulate logs processing
```
Above, log entries with a level of `warning` or more (see the [Log levels](#log-levels) for the order) will be caught by this logger. `setCascading()` was set to *false* (default is *true*) so the entries caught here won't continue downstream past that particular bucket. `setDeferred()` was set to *true* (default is *false*) so processing happen on `__destruct` (end of script generally) rather than on the fly. 

Now, lets create a main logger object and inject the two loggers.
```php
// The main logger object (injecting the previous loggers/buckets)
$logger = new Logger( array($urgent_logger, $app_logger) );
```
Lets create an additional logger -- just for development/debug purposes.
```php
if(DEBUG) {
  // Bucket for the remaining logs -- i.e. `notice`, `info` and `debug`
  $dev_logger = new Logger\Stream(); // default to screen without output buffer  
  // $dev_logger = new Logger\File('/tmp/apix_debug.log'); 
  $dev_logger->setMinLevel('debug');

  $logger->add($dev_logger);   		// another way to inject a bucket
}
```
Finally, lets push some log entries:

```php
// handled by both $urgent_logger & $app_logger
$e = new \Exception('Boo!');
$logger->critical('OMG saw {bad-exception}', [ 'bad-exception' => $e ]);

// handled by $app_logger
$logger->error($e); // push an object (or array) directly

// handled by $dev_logger
$logger->info('Testing a var {my_var}', array('my_var' => array(...)));
```

Log levels
----------
The eight [RFC 5424][] levels of logs are supported, in cascading order:

 Severity  | Description
-----------|-----------------------------------------------------------------
 Emergency | System level failure (not application level)
 Alert     | Failure that requires immediate attention
 Critical  | Serious failure at the application level 
 Error     | Runtime errors, used to log unhandled exceptions
 Warning   | May indicate that an error will occur if action is not taken
 Notice    | Events that are unusual but not error conditions
 Info      | Normal operational messages (no action required)
 Debug     | Verbose info useful to developers for debugging purposes (default)

[PSR-3]: http://tools.ietf.org/html/rfc5424
[RFC 5424]: http://tools.ietf.org/html/rfc5424#section-6.2.1

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
