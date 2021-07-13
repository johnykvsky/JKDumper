# JKDumper

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-github-actions]][link-github-actions]

JKDumper is one-man-army quick'n'dirty debugging-logging tool. Good for both:
- legacy applications, where you have spaghetti code
- new projects, where you need to inject logger through 4 services just to dump some variables fot one time.

JKDumper is one file with optional logging library. Yes, it use singleton instance(), yes, it has static methods - it's just a tool and it need to be dead-simple to use in whatever sh**ty code we have to work.

## Install

Via Composer

``` bash
$ composer require johnykvsky/jkdumper
```

Should work fine on PHP 5.6, but I didn't check that. Just change required PHP version in composer.json and maybe remove dev packages.

## Features

- Dumping variables to screen, basic formatting for easy reading
- Detecting if run from CLI (for proper formatting)
- Optional: logging variables to file (iw. when we want to check some process, not single thing)
- Optional: simple benchmarking via microtime()

If logger is set, then benchmarking is logged. Library use var_dump for dumping data, if needed it disable Xdebug 'overload_var_dump' (only for single dump, it is restored).

## Usage

TL/DR: Nice way to make it a bit easier is to create function, ie. right after autoload:

```
require '../vendor/autoload.php';

if (!function_exists('dde')) {
    function dde($var, $die = false) {
        \johnykvsky\Utils\JKDumper::instance()->vdump($var, true);
        if ($die) {
        	die;
        }
    }
}

```

This will allow us to dump variable to screen like this, with optional stopping further execution (second parameter):

```
dde($myVariable);
dde($myOtherVariable,1);

```

Full example:

``` php
//dump variables:
$dumper = new \johnykvsky\Utils\JKDumper();
echo $dumper->vdump('test');

//or, better for dirty debugging:
echo \johnykvsky\Utils\JKDumper::instance()->vdump('test');

//if you pass true as second parameter to vdump, result will be in <pre></pre> tags (if in CLI, PHP_EOL will be added)
echo \johnykvsky\Utils\JKDumper::instance()->vdump('test', true);
//result: <pre>test</pre>

//benchmark start, parameter is optional
\johnykvsky\Utils\JKDumper::instance()->startTime('usersQuery'); //info is also written into logs, if logger is set
//do stomething, lets rest
sleep(3);
//stop and check results - we can run few benchmarks in the same time and get results by parameter name,
//so passed parameter must be the same as we provided in startTime
echo \johnykvsky\Utils\JKDumper::instance()->endTime('usersQuery'); //this is also written into logs, if logger is set

//logging, somewhere in the code we need to create logger and pass it to JKDumper
//usually it's good to do it somewhere at the beginning. index.php? base controller?
//for JKLogger parameter is path where we want to store logs
$logger = new \johnykvsky\Utils\JKLogger(__DIR__.'/files');
\johnykvsky\Utils\JKDumper::instance()->setLogger($logger);

//and now we ca dump variables to logs anywhere, anytime
\johnykvsky\Utils\JKDumper::instance()->log($request);

//you can store results:
$data['req'] = \johnykvsky\Utils\JKDumper::instance()->vdump($request);
$data['res'] = \johnykvsky\Utils\JKDumper::instance()->vdump($response);
//and log them later in one go:
\johnykvsky\Utils\JKDumper::instance()->log($data);


```

Log file example:

```
[2017-12-25 22:55:48.108006] [usersQuery] Started timing usersQuery
[2017-12-25 22:55:48.108288] [usersQuery] Finished debug in 0.279 milliseconds
[2017-12-25 22:55:48.124536] [debug] 'test'
```

It's good to look at log library to see how it works and how it can be customized: [jklogger][link-jklogger]

This is my choice of logger, but it can be replaced with anything compatibile with PSR-3 LoggerInterface

## Testing

``` bash
$ composer test
```

## Code checking

``` bash
$ composer phpstan
$ composer phpstan-max
```

## Security

If you discover any security related issues, please email johnykvsky@protonmail.com instead of using the issue tracker.

## Credits

- [johnykvsky][link-author]
- [katzgrau][link-klogger]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/johnykvsky/JKDumper.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/johnykvsky/JKDumper.svg?style=flat-square
[ico-github-actions]: https://github.com/johnykvsky/JKDumper/actions/workflows/php.yml/badge.svg

[link-packagist]: https://packagist.org/packages/johnykvsky/JKDumper
[link-downloads]: https://packagist.org/packages/johnykvsky/JKDumper
[link-author]: https://github.com/johnykvsky
[link-jklogger]: https://github.com/johnykvsky/JKLogger
[link-github-actions]: https://github.com/johnykvsky/JKDumper/actions
