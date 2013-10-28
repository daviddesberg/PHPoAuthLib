PHPoAuthLib
===========
PHPoAuthLib provides oAuth support in PHP 5.3+ and is very easy to integrate with any project which requires an oAuth client.

[![Build Status](https://travis-ci.org/Lusitanian/PHPoAuthLib.png?branch=master)](https://travis-ci.org/Lusitanian/PHPoAuthLib)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Lusitanian/PHPoAuthLib/badges/quality-score.png?s=c5976d2fefceb501f0d886c1a5bf087e69b44533)](https://scrutinizer-ci.com/g/Lusitanian/PHPoAuthLib/)
[![Latest Stable Version](https://poser.pugx.org/lusitanian/oauth/v/stable.png)](https://packagist.org/packages/lusitanian/oauth)
[![Total Downloads](https://poser.pugx.org/lusitanian/oauth/downloads.png)](https://packagist.org/packages/lusitanian/oauth)

Installation
------------
This library can be found on [Packagist](https://packagist.org/packages/lusitanian/oauth).
The recommended way to install this is through [composer](http://getcomposer.org).

Edit your `composer.json` and add:

```json
{
    "require": {
        "lusitanian/oauth": "~0.2"
    }
}
```

And install dependencies:

```bash
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

Features
--------
- PSR-0 compliant for easy interoperability
- Fully extensible in every facet.
    - You can implement any service with any custom requirements by extending the protocol version's `AbstractService` implementation.
    - You can use any HTTP client you desire, just create a class utilizing it which implements `OAuth\Common\Http\ClientInterface` (two implementations are included)
    - You can use any storage mechanism for tokens. By default, session, in-memory and Redis.io (requires PHPRedis) storage mechanisms are included. Implement additional mechanisms by implementing `OAuth\Common\Token\TokenStorageInterface`.

Service support
---------------
The library supports both oAuth 1.x and oAuth 2.0 compliant services. A list of currently implemented services can be found below. 

Included service implementations
--------------------------------
- OAuth1
    - Twitter
    - Tumblr
    - FitBit
    - Etsy
    - Flickr
- OAuth2
    - Google
    - Microsoft
    - Facebook
    - GitHub
    - BitLy
    - Yammer
    - SoundCloud
    - Foursquare
    - Instagram
    - LinkedIn
    - Box
    - Vkontakte
    - Amazon
    - PayPal
    - Dropbox
    - Dailymotion
    - Heroku
- more to come!

Examples
--------
Examples of basic usage are located in the examples/ directory.

Usage
------
For usage with complete auth flow, please see the examples. More in-depth documentation will come with release 1.0.

Framework Integration
---------------------
* Lithium: Sébastien Charrier has written [an adapter](https://github.com/scharrier/li3_socialauth) for the library.

Tests
------
To run the tests, you must install dependencies with `composer install --dev`
