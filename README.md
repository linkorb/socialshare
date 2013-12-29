# LinkORB SocialShare Library

Retrieve sharing stats for any url for various social networks.

## Simple example

```php
$url = new LinkORB\Component\SocialShare\Url('http://google.com');
print_r($url->getShareCounts();
```
Output:

```
Array
(
    [pinterest] => Array
        (
            [shares] => 10609
        )

    [twitter] => Array
        (
            [shares] => 7485
        )

    [facebook] => Array
        (
            [id] => http://google.com
            [comments] => 133545
            [shares] => 8093503
            [likes] =>
            [talking_about_count] =>
            [were_here_count] =>
            [count] => 8227048
        )

    [linkedin] => Array
        (
            [count] => 63420
        )

)
```

## Features

* PSR-0 compatible, works with composer and is registered on packagist.org
* Optional caching support through any Doctrine CacheProvider (FileSystem, Memcached, etc..)
* Support for twitter.com
* Support for facebook.com
* Support for linkedin.com
* Support for pinterest.com

## Installing

Check out [composer](http://www.getcomposer.org) for details about installing and running composer.

Then, add `linkorb/socialshare` to your project's `composer.json`:

```json
{
    "require": {
        "linkorb/socialshare": "dev-master"
    }
}
```

## Try the commandline utility:

There is a simple example commandline utility to test the functionality:

    bin/console socialshare:urlsharecount http://google.com

This will retrieve all supported sharing stats, and output the resulting array to the console.

## Contributing

Ready to build and improve on this repo? Excellent!
Go ahead and fork/clone this repo and we're looking forward to your pull requests!

If you are unable to implement changes you like yourself, don't hesitate to
open a new issue report so that we or others may take care of it.

## Todo

* Add support for more networks (Tumblr, Google+, etc...)

## License
Please check LICENSE.md for full license information


