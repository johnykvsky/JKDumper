# EstimateCalculator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Simple estimation module, credits goes to Piotr Pasich and his [article](http://piotrpasich.com/how-to-meet-your-estimations-with-the-deadline/)

## Install

Via Composer

``` bash
$ composer require johnykvsky/estimatecalculator
```

## Usage

``` php
$calc = new johnykvsky\EstimateCalculator(4,16,8);
echo $calc->getApproximation();
```

EstimateCalculator needs three input parameters:
- Optimistic estimation
- Pessimistic estimation
- Most likely estimation

In example we provided 4, 16 and 8 hours. Now we can try to calculate:

``` php
echo $calc->getApproximation(); //basic aproximation
```

The [PERT](http://tynerblain.com/blog/2009/06/18/advanced-pert-estimation/) says that to increase our confidence about the result we need to sum our estimations with multiplied standard deviation. When we add our E + SD the accuracy of result will be about 68%, but if we multiplied the SD by 1,6 we will reach 90% certainly

``` php
echo $calc->getStandardDeviation(); //standard deviation
echo $calc->get68accuracy(); //68% accuracy
echo $calc->get90accuracy(); //90% accuracy
```


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email johnykvsky@protonmail.com instead of using the issue tracker.

## Credits

- [johnykvsky][link-author]
- [Piotr Pasich](http://piotrpasich.com/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/johnykvsky/EstimateCalculator.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/johnykvsky/EstimateCalculator/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/johnykvsky/EstimateCalculator.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/johnykvsky/EstimateCalculator.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/johnykvsky/EstimateCalculator.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/johnykvsky/EstimateCalculator
[link-travis]: https://travis-ci.org/johnykvsky/EstimateCalculator
[link-scrutinizer]: https://scrutinizer-ci.com/g/johnykvsky/EstimateCalculator/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/johnykvsky/EstimateCalculator
[link-downloads]: https://packagist.org/packages/johnykvsky/EstimateCalculator
[link-author]: https://github.com/johnykvsky
