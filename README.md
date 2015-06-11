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

  $logger = new Logger\File('/var/log/apix.log');
  $logger->setMinLevel('alert'); // same as Psr\Log\LogLevel::ALERT

  // then later, just push/send an alert...  
  $logger->alert('Running out of {product} ', array('product'=>'beer'));
```

Advanced usage (*multi-logs dispatcher*)
--------------

```php
  use Apix\Log;
  
  $logger = new Logger();
  
  // The log bucket for critical, alert and emergency.
  $urgent_log = new Logger\Mail('foo@bar.boo');
  $urgent_log->setMinLevel('critical');  // same Psr\Log\LogLevel::CRITICAL
  
  // The log bucket for notice, warning and error. 
  $prod_log = new Log\Logger\File('/var/log/apix_prod.log');
  $prod_log->setMinLevel('notice');  // same Psr\Log\LogLevel::NOTICE
  
  $this->logger->add($urgent_log);
  $this->logger->add($prod_log);

  if (DEBUG) {
      // The develop log bucket for info and debug
      $dev_log = new Log\Logger\File('/tmp/apix_develop.log');
      $dev_log->setMinLevel('debug');  // same as Psr\Log\LogLevel::DEBUG

      $logger->add($dev_log);
  }

  // then (later) notify the loggers...
  $this->logger->notice('Some notice...');
  $this->logger->critical('Blahh blahh...');
```

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
