PHPoAuthLib Readme
======
PHPoAuthLib provides oAuth support in PHP 5.4+ and is very easy to integrate with any project which requires an oAuth client. 

Features
--------
- PSR-0 compliant for easy interoperability
- Fully extensible in every facet.
   - You can implement any service with any custom requirements by extending the protocol version's `AbstractService` implementation.
   - You can use any HTTP client you desire, just create a class utilizing it which implements `OAuth\Common\Http\ClientInterface` (implementations for [Artax](https://github.com/rdlowrey/Artax/) and the built in PHP HTTP wrapper are included)
   - You can use any storage mechanism for tokens. By default, session and "Null" storage mechanisms are included. Implement additional mechanisms by implementing `OAuth\Common\Token\TokenStorageInterface`. 

Service support
----------------
The library currently works with any oAuth 2.0 compliant service. oAuth 1.x support is in the works.

Included service implementations
------------------
 - OAuth2
   - Google
   - GitHub
   - BitLy
 - more to come!

Usage
------
An example for logging in with Google is included in examples/google.php
Authorizing a user with any service is very concise:

```php
<?php
$storage = new Null();
$credentials = new Credentials(GOOGLE_CLIENT, GOOGLE_SECRET, get_own_url() );
$googleService = new Google(new OAuth\Common\Consumer\Credentials('yourClient', 'yourSecret', 'yourCallBackUrl'), new OAuth\Common\Http\StreamClient(), new OAuth\Common\Storage\Null(), [ Google::SCOPE_EMAIL, Google::SCOPE_PROFILE ]);
header('Location: ' . $googleService->getAuthorizationUrl());
```
To handle the callback and obtain the token:
```php
<?php
$token = $googleService->requestAccessToken( $_GET['code'] ); // note that the token will also be passed to the `TokenStorageInterface` passed to the service
// get userinfo
$result = json_decode( $googleService->sendAuthenticatedRequest( new Uri('https://www.googleapis.com/oauth2/v1/userinfo'), [], 'GET' ), true );
```
