# Cookie Wrapper

# Example

```php
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;

$cookieOptions = new Options();
$cookieOptions->setDomain('example.com'); //default: false (localhost)
$cookieOptions->setPath('/'); //default: '' (string empty)
$cookieOptions->setSecure(true); //default: false
$cookieOptions->setHttponly(true); //default: false
$cookieOptions->setSameSite('Strict'); //default: Lax

$cookie = new Cookie($cookieOptions);

$cookie->set('key', 'value<>', $ttl = true, $options = []);
$cookie->setRaw('key', 'value<>');
$cookie->delete('key');
Cookie::get('key');
```