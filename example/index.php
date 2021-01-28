<?php

use Enjoys\Cookie\Cookie;

include __DIR__."/../vendor/autoload.php";
//
//
//$cookieOptions = new \Enjoys\Cookie\Options();
//$cookieOptions->setDomain(false);
//$cookieOptions->setPath('/');
//$cookieOptions->setHttponly(true);

$cookie = new Cookie(new \Enjoys\Cookie\Options());



$cookie->set('token', '<>', 'session');
$cookie->setRaw('token4', '<>', 'session');
$cookie->set('token2', time(), 3600, [
'samesite' => 'strict'
]);
var_dump(Cookie::has('token4'));
//var_dump($cookie);
//var_dump($_COOKIE);
