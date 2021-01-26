<?php

use Enjoys\Cookie\Cookie;

include __DIR__."/../vendor/autoload.php";
//
$cookie = new Cookie();
$cookie->setDomain();
$cookie->setPath('/');

$cookie->set('token', time(), 'session');
$cookie->set('token2', time()+500000000, 'session');

var_dump($cookie);
