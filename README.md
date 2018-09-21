#AppNexusClient
[![Total Downloads](https://img.shields.io/packagist/dt/f3ath/appnexus.svg)](https://packagist.org/packages/f3ath/appnexus)
[![Latest Stable Version](https://img.shields.io/packagist/v/f3ath/appnexus.svg)](https://packagist.org/packages/f3ath/appnexus)
[![Travis Build](https://travis-ci.org/f3ath/appnexusclient.svg?branch=master)](https://travis-ci.org/f3ath/appnexusclient)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/3637a8cf-8735-465a-b528-a4ad1edff017.svg)](https://insight.sensiolabs.com/projects/3637a8cf-8735-465a-b528-a4ad1edff017)

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
