# Cookie Wrapper

# Example

```php
use Enjoys\Cookie\Cookie;

$cookie = new Cookie();
$cookie->setDomain('example.com'); //default: false (localhost)
$cookie->setPath('/'); //default: '' (string empty)
$cookie->setSecure(true); //default: false
$cookie->setHttponly(true); //default: false
$cookie->setSameSite('Strict'); //default: Lax

$cookie->set('key', 'value', $ttl = 0, $options = []);
$cookie->delete('key');
Cookie::get('key');
```