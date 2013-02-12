PHPoAuthLib Readme
======
PHPoAuthLib provides oAuth support in PHP 5.4+ and is very easy to integrate with any project which requires an oAuth client. 

Features
--------
- PSR-0 compliant for easy interoperability
- Fully extensible in every facet.
   - You can implement any service with any custom requirements by extending the protocol version's `AbstractService` implementation.
   - You can use any HTTP client you desire, just create a class utilizing it which implements `OAuth\Common\Http\ClientInterface` (a stream-based implementation is included)
   - You can use any storage mechanism for tokens. By default, session, in-memory and Redis.io (requires PHPRedis) storage mechanisms are included. Implement additional mechanisms by implementing `OAuth\Common\Token\TokenStorageInterface`. 

Service support
----------------
The library supports both oAuth 1.x and oAuth 2.0 compliant services. A list of currently implemented services can be found below. More services will be implemented soon.

Included service implementations
------------------
 - OAuth1
   - Twitter
 - OAuth2
   - Google
   - Microsoft
   - GitHub
   - BitLy
   - Yammer
   - SoundCloud
   - Foursquare
 - more to come!

Examples
--------
Examples of basic usage are located in the examples/ directory.

Usage
------
For usage with complete auth flow, please see the examples. More in-depth documentation will come with release 1.0.


Tests
------
To run the tests, you must install dependencies with `composer install --dev`