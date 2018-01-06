# JKDumper

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

JKDumper is one-man-army quick'n'dirty debug tool. Good for both:
- legacy applications, where you have spaghetti code
- new projects where you need to inject logger through 4 services just to dump some variables fot one time.

JKDumper is one file with optional logging library. Yes, it use singleton instance(), yes, it has static methods - it's just a tool and it need to be dead-simple to use.

## Install

Via Composer

``` bash
$ composer require johnykvsky/jkdumper
```

## Features

- Dumping variables
- Optional: logging variables to file
- Optional: simple benchmarking via microtime()

If logger is set, then benchmarking is logged. Library use var_dump for sumping data, but if Xdebug is present, then own internal methods are used (sine Xdebug give a lot more than simple variable content)

## Usage

``` php
//dump variables
$dumper = new johnykvsky\Utils\JKDumper();
echo $dumper::vdump('test');

//benchmark, parameter is optional
$dmp->startTime('usersQuery'); //this is also written into logs, if logger is set
//do sth
//stop and check results, parameter must be the same as in startTime
echo $dmp->endTime('usersQuery'); //this is also written into logs, if logger is set

//logging
$logger = new \Katzgrau\KLogger\Logger(__DIR__.'/files');
$dmp->setLogger($logger)
$dmp->log($request);
//or, in other file, PHP7
johnykvsky\Utils\JKDumper::instance()::log($response);
johnykvsky\Utils\JKDumper::instance()::vdump($parameters);
//or, in other file, PHP5
$dmpr = johnykvsky\Utils\JKDumper::instance();
$dmpr::log($response);
$dmpr::vdump($query);

```

Log file example:

```
[2017-12-25 22:55:48.108006] [usersQuery] Started timing debug
[2017-12-25 22:55:48.108288] [usersQuery] Finished debug in 0.279 milliseconds
[2017-12-25 22:55:48.124536] [debug] 'test'
```


## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email johnykvsky@protonmail.com instead of using the issue tracker.

## Credits

- [johnykvsky][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/johnykvsky/JKDumper.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/johnykvsky/JKDumper/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/johnykvsky/JKDumper.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/johnykvsky/JKDumper.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/johnykvsky/JKDumper.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/johnykvsky/JKDumper
[link-travis]: https://travis-ci.org/johnykvsky/JKDumper
[link-scrutinizer]: https://scrutinizer-ci.com/g/johnykvsky/JKDumper/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/johnykvsky/JKDumper
[link-downloads]: https://packagist.org/packages/johnykvsky/JKDumper
[link-author]: https://github.com/johnykvsky
