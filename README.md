# LinkORB SocialShare Library

Retrieve sharing stats for any url for various social networks.

## Simple example

```php
$url = new LinkORB\Component\SocialShare\Url('http://google.com');
print_r($url->getShareCounts());
```
Output:

```
Array
(
    [pinterest] => Array
        (
            [count] => 10610
            [countlabel] => 10.6K
        )

    [twitter] => Array
        (
            [count] => 7485
            [countlabel] => 7.5K
        )

    [facebook] => Array
        (
            [id] => http://google.com
            [comments] => 133545
            [shares] => 2
            [likes] =>
            [talking_about_count] =>
            [were_here_count] =>
            [count] => 133547
            [countlabel] => 133.5K
        )

    [linkedin] => Array
        (
            [count] => 63420
            [countlabel] => 63.4K
        )

    [googleplus] => Array
        (
            [count] => 5142375
            [countlabel] => 5142.4K
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
* Support for plus.google.com

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

* Add support for more networks (Tumblr, QQ, etc...)

## License
Please check LICENSE.md for full license information

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!

