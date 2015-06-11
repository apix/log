APIx Log, very thin PSR-3 logger
================================
[![Latest Stable Version](https://poser.pugx.org/apix/log/v/stable.svg)](https://packagist.org/packages/apix/log)  [![Build Status](https://travis-ci.org/frqnck/apix-log.png?branch=master)](https://travis-ci.org/frqnck/apix-log)  [![Code Quality](https://scrutinizer-ci.com/g/frqnck/apix-log/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/frqnck/apix-log/?branch=master)  [![Code Coverage](https://scrutinizer-ci.com/g/frqnck/apix-log/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/frqnck/apix-log/?branch=master)  [![License](https://poser.pugx.org/apix/log/license.svg)](https://packagist.org/packages/apix/log)

Minimalist **PSR-3** compliant logger.

* Unit **tested** and compliant with PSR0, PSR1 and PSR2.
* Continuously integrated against **PHP 5.3**, **5.4**, **5.5** and **5.6**.
* Available as a **[Composer](http://https://packagist.org/packages/apix/log)** and as a **[PEAR](http://pear.ouarz.net)** package.

Feel free to comment, send pull requests and patches...

Basic usage (*standalone*)
-----------
```php
use Apix\Log;

// Bucket for log superior or equal to `critical`
$urgent_logger = new Logger\Mail('franck@foo.bar');
$urgent_logger->setMinLevel('critical');    // set the minimal level
```

This logger/bucket will intercepts `critical`, `alert` and `emergency` logs.

Eventually to log an event, use:

```php
$urgent_logger->alert('Running out of {stuff}', ['stuff' => 'beers']);
```

Advanced usage (*multi-logs dispatcher*)
--------------
Okay. Lets create some additional loggers/buckets -- one generic, another one for development.

```php
// Bucket for log >= to `notice`
$app_logger = new Logger\File('/var/log/apix_app.log');
$app_logger->setMinLevel('notice')
            ->setCascading(False);    // stop the log here if intercepted

// The main logger object (injecting the buckets)
$logger = new Logger( array($urgent_logger, $app_logger) );

if(DEBUG) {
  // Bucket log just for `info` and `debug`
  $debug_logger = new Log\Logger\File('/tmp/apix_develop.log');
  $debug_logger->setMinLevel('debug');

  $logger->add($debug_logger);    // another way to inject a bucket
}
```

Note that `setCascading()` was set to False (default is True) which means that any intercepted log entries won't continue downstream pass that particular bucket. So in that case, the debug bucket will only get `info` and `debug` logs.

Finally, you can push some log entries in the following manners:

```php
$logger->notice('Something happen -> {ctx}', array('ctx' => array(...) ) );
  
$e = New \Exception('boo!');
$logger->critical('OMG saw {exception}', [ 'exception' => $e ]);

$logger->debug($e);     // or push an object, an array directly
```

Log levels
----------
The eight [RFC 5424][] levels of logs are supported, in order:

Severity  | Description
----------|------------
emergency | System level failure (not application level)
alert     | Failure that require immediate attention
critical  | Serious failure at the application level
error     | Runtime errors, used to log unhandled exceptions
warning   | May indicate that an error will occur if action is not taken
notice    | Events that are unusual but not error conditions
info      | Normal operational messages (no action required)
debug     | Verbose infos useful to developers for debugging purposes

[PSR-3]: http://tools.ietf.org/html/rfc5424
[RFC 5424]: http://tools.ietf.org/html/rfc5424

Installation
------------------------

* If you are creating a component that relies on APIx Log locally:

  * either update your **`composer.json`** file:

    ```json
    {
      "require": {
        "apix/log": "1.0.*"
      }
    }
    ```

  * or update your **`package.xml`** file as follow:

    ```xml
    <dependencies>
      <required>
        <package>
          <name>apix_log</name>
          <channel>pear.ouarz.net</channel>
          <min>1.0.0</min>
          <max>1.999.9999</max>
        </package>
      </required>
    </dependencies>
    ```
* For a system-wide installation using PEAR:

    ```
    sudo pear channel-discover pear.ouarz.net
    sudo pear install --alldeps ouarz/apix_log
    ```
For more details see [pear.ouarz.net](http://pear.ouarz.net).

License
-------
APIx Log is licensed under the New BSD license -- see the `LICENSE.txt` for the full license details.
