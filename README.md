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
Authorizing a user with any service is very concise:

```php
<?php
$storage = new OAuth\Common\Storage\Memory();
$credentials = new Credentials(GOOGLE_CLIENT, GOOGLE_SECRET, get_own_url() );
$googleService = new Google(new OAuth\Common\Consumer\Credentials('yourClient', 'yourSecret', 'yourCallBackUrl'), new OAuth\Common\Http\StreamClient(), new OAuth\Common\Storage\Null(), [ Google::SCOPE_EMAIL, Google::SCOPE_PROFILE ]);
header('Location: ' . $googleService->getAuthorizationUri());
```
To handle the callback and obtain the token:
```php
<?php
$token = $googleService->requestAccessToken( $_GET['code'] ); // note that the token will also be passed to the `TokenStorageInterface` passed to the service
// get userinfo
$result = json_decode( $googleService->sendAuthenticatedRequest( new Uri('https://www.googleapis.com/oauth2/v1/userinfo'), [], 'GET' ), true );
```

API Docs
---------
View the API docs [here](http://lusitanian.github.com/PHPoAuthLib/doc/api/).
