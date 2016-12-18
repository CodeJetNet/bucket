# Bucket

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

         .=======.
        /         \
       /   _____   \
      /.-'"     "`-.\
     [(             )]
      |`-.._____..-'|
      |             |
      |             |
      |   bucket    |
      \             /
       `-.._____..-'


A convenient [container-interop][link-container-interop] compatible DI container object.  

Easy to use, easy to understand and inherently easy to extend.

## Install

Via Composer

``` bash
$ composer require codejet/bucket
```

## Usage

### Creating a bucket
``` php
$bucket = new CodeJet\Bucket\Bucket();
```

### Adding Values

Using a string as the key, pass any value that is not a `\Closure` and it will be stored as-is.

``` php
$bucket->add('value-id', 'The value of the value.');
```

### Adding Factories

Using a string as the key and passing a `\Closure` as the value will store a factory.  The Closure may accept `\Interop\Container\ContainerInterface` as it's only argument. The bucket will pass itself (or the assigned delegate) in to the factory when `$bucket->get('service-id')` is called the first time and it will store the returned data as the value for subsequent requests for the same id.

``` php
$bucket->add(
    'service-id',
    function (\Interop\Container\ContainerInterface $bucket) {
        return new \stdClass();
    }
);
```

### Retrieving Items
``` php
var_dump($bucket->has('value-id')); // bool(true)
var_dump($bucket->get('value-id')); // string(23) "The value of the value."

var_dump($bucket->has('service-id')); // bool(true)
var_dump($bucket->get('service-id')); // class stdClass#4 (0) { }
```

### Delegate lookup feature

The [container-interop delegate lookup standard][delegate-lookup-std-link] provides a means 
through which a container may use an alternate container for dependency injection purposes.

```php
$delegateLookupContainer = new \League\Container\Container();
$delegateLookupContainer->add('importantSetting', 'This value is only found in the delegate container.');

$bucket = new \CodeJet\Bucket\Bucket();
$bucket->setDelegateContainer($delegateLookupContainer);
$bucket->add(
    'service-id',
    function (\Interop\Container\ContainerInterface $container) {
        // The factory Closure is passed the delegate lookup container.
        return $container->get('importantSetting');
    }
);

var_dump($bucket->get('service-id')); // string(51) "This value is only found in the delegate container."
var_dump($bucket->has('importantSetting')); // bool(false)
```

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email josh@findsomehelp.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/codejet/bucket.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/CodeJetNet/bucket/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/CodeJetNet/bucket.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/CodeJetNet/bucket.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/codejet/bucket.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/codejet/bucket
[link-travis]: https://travis-ci.org/CodeJetNet/bucket
[link-scrutinizer]: https://scrutinizer-ci.com/g/CodeJetNet/bucket/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/CodeJetNet/bucket
[link-downloads]: https://packagist.org/packages/codejet/bucket

[link-container-interop]: https://github.com/container-interop/container-interop
[delegate-lookup-std-link]: https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup.md