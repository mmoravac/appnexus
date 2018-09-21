#AppNexusClient

A simple Appnexus API client

#Install
Via [composer](https://getcomposer.org):
`$ composer require "mmoravac/appnexus"`

#Use
```php
$storage = new MMoravac\AppNexusClient\ArrayTokenStorage(); // Memcached and Apc storage are also available
$appnexus = new MMoravac\AppNexusClient\AppNexusClient('username', 'password', "http://api-console.client-testing.adnxs.net/", $storage);
var_dump($appnexus->call(MMoravac\AppNexusClient\HttpMethod::GET, '/user'));
```
