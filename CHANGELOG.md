# APIx-log changelog

#### Version 1.1.1 (11-Jun-2015)
- Updated the REAME.md
- Fixed setCascading (just uncommented).

#### Version 1.1.0 (11-Jun-2015)
- Fixed a PHP 5.3 specific syntax error (unit-test)
- Renamed `Apix\Log\Logger\Null` to `Apix\Log\Logger\Nil`. 'Null' as a classname is now reserved to PHP7 usage, see [PHP RFC: Reserve More Types in PHP 7](https://wiki.php.net/rfc/reserve_more_types_in_php_7)
- Some semantic modifications e.g. now using "Log Buckets" to holds loggers. 
- Added bucket self-prioritization as opposed to the FIFO mode used until now.
- Fixed the cascading or not of log entries to subsequent buckets.
- Added some aditional tests -- 100% code coverage!

#### Version 1.0.2 (10-Jun-2015)
- Added the logged message can be the context directly i.e. not a string. 
- Added `\InvalidArgumentException` with an explicit message to the main constructor.
- Added handling of Exception as context e.g. `$logger()->critical( new \Exception('Boo!') )`. 

#### Version 1.0.1 (9-Jun-2015)
- Added Scrutinizer checks.
- Added `.gitattributes` file.
- Added a unit tests `bootstrap.php` file.
- Added a default timezone to the unit tests bootstraper.
- Fixed the context array handler (convert data to JSON). 
- Added additional tests and minor changes.
- Updated the examples in `README.md`.
- Added a `CHANGELOG.md` file.

#### Version 1.0.0 (30-Sept-2014)
- Initial release.

<pre>
  _|_|    _|_|    _|     _|      _|
_|    _| _|    _|         _|    _|
_|    _| _|    _| _|        _|_|
_|_|_|_| _|_|_|   _| _|_|   _|_|
_|    _| _|       _|      _|    _|
_|    _| _|       _|     _|      _|
</pre>
