# APIx Log changelog

#### Version `1.2.2` (XX-Oct-2016)
- Updated to be compatible with latest PHPUnit.
- Updated to the latest PSR-3 (PSR Log) version.
- Added link to `apix/log-tracker`.

#### Version `1.2.1` (15-Oct-2015)
- Added `\LogicException` when the stream has been `__destruct()` too early.

#### Version `1.2.0` (18-Sep-2015)
NOTE: The major version number update is due to some internal changes. The actual client methods have not been changed, i.e. has the same signatures as the `1.1.*` branch.
- Changes to the handling and processing of each individual log entry (resulting in memory and CPU optimisation).
- Refactored and documented `LoggerInterface` (better API for contribution).
- Added `LogFormatter` class.
- Changed the ordering of the log levels to match [RFC5424](http://tools.ietf.org/html/rfc5424#section-6.2.1) (thanks @jspalink).
- Added a `Stream` logger.
- Added functional tests to support the README's examples.
- Added link to `jspalink/apix-log-pushover`.
- Updated the README.md

#### Version `1.1.4` (10-Sep-2015)
- Added link to `PHPMailer/apix-log-phpmailer`.
- Bug fixes.

#### Version `1.1.3` (10-Sep-2015)
- Added `setDeferred` so processing of logs happen at `__destruct` time on a bucket and/or logger level (thanks @Synchro). 
- Updated the README.md accordingly.

#### Version `1.1.2` (28-Aug-2015)
- Updated the README.md
- Added HHVM support.
- Updated PHPUnit to 4.8 version.
- Added PHP7 support.
- Made Travis faster (using Docker containers and skipping allowable failures).

#### Version `1.1.1` (11-Jun-2015)
- Updated the README.md
- Fixed setCascading (just uncommented).

#### Version `1.1.0` (11-Jun-2015)
- Fixed a PHP 5.3 specific syntax error (unit-test)
- Renamed `Apix\Log\Logger\Null` to `Apix\Log\Logger\Nil`. 'Null' as a classname is now reserved to PHP7 usage, see [PHP RFC: Reserve More Types in PHP 7](https://wiki.php.net/rfc/reserve_more_types_in_php_7)
- Some semantic modifications e.g. now using "Log Buckets" to holds loggers. 
- Added bucket self-prioritization as opposed to the FIFO mode used until now.
- Fixed the cascading or not of log entries to subsequent loggers.
- Added some aditional tests -- 100% code coverage!

#### Version `1.0.2` (10-Jun-2015)
- Added the logged message can be the context directly i.e. not a string. 
- Added `\InvalidArgumentException` with an explicit message to the main constructor.
- Added handling of Exception as context e.g. `$logger->critical( new \Exception('Boo!') )`. 

#### Version `1.0.1` (9-Jun-2015)
- Added Scrutinizer checks.
- Added `.gitattributes` file.
- Added a unit tests `bootstrap.php` file.
- Added a default timezone to the unit tests bootstrapper.
- Fixed the context array handler (convert data to JSON). 
- Added additional tests and minor changes.
- Updated the examples in `README.md`.
- Added a `CHANGELOG.md` file.

#### Version `1.0.0` (30-Sept-2014)
- Initial release.

<pre>
  _|_|    _|_|    _|     _|      _|
_|    _| _|    _|         _|    _|
_|    _| _|    _| _|        _|_|
_|_|_|_| _|_|_|   _| _|_|   _|_|
_|    _| _|       _|      _|    _|
_|    _| _|       _|     _|      _|
</pre>
