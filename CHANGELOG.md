# Changelog for [HuffAndPuff fork of PHPoAuthLib](https://github.com/HuffAndPuff/PHPoAuthLib)

## 0.4.1

SoundCloud service has scopes now.

## 0.4.0

### New services

* Vimeo
* Yahoo
* Scoop.it!

### New features

* Added magic wl.contacts_emails scope to Microsoft service for email contacts.
* More Facebook scopes.
* Adding ability to specify access_type=offline for Google.
* Add read-only scope for Google calendar.
* Update YouTube (Google) scopes

### Bugfixes

* Changed HTTP header to "Content-Type" instead of "Content-type".
* Namespace fix in Salesforce.
* Session class does not close session if it did not start it.
* Fixed namespace in Salesforce class.
* Fix oauth_verifier not sent

## 0.3
Forked from [Lusitanian/PHPoAuthLib](https://github.com/Lusitanian/PHPoAuthLib)

