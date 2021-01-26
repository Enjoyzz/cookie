<?php

use Enjoys\Cookie\Cookie;

include __DIR__."/../vendor/autoload.php";
//
$cookie = new Cookie();
$cookie->setDomain(false);
$cookie->setPath('/');
$cookie->setHttponly(true);


$cookie->set('token', time(), 'session');
$cookie->set('token2', time()+500000000, 3600, [
'samesite' => 'strict'
]);
$cookie->set('token3', time(), 'session');
//var_dump($cookie);
//var_dump($_COOKIE);
