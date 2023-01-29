[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FEnjoyzz%2Fcookie%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/Enjoyzz/cookie/master)
[![tests](https://github.com/Enjoyzz/cookie/actions/workflows/test.yml/badge.svg)](https://github.com/Enjoyzz/cookie/actions/workflows/test.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Enjoyzz/cookie/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Enjoyzz/cookie/?branch=master)
# Cookie Wrapper

## Example

В Options устанавливаются глобальные параметры. При установке cookie можно будет изменить эти настройки для конкретного
случая.

```php
use Enjoys\Cookie\Options;

/** @var \Psr\Http\Message\ServerRequestInterface $request */
$cookieOptions = new Options($request);
$cookieOptions->setDomain('example.com'); //default: false (localhost)
$cookieOptions->setPath('/'); //default: '' (string empty)
$cookieOptions->setSecure(true); //default: false
$cookieOptions->setHttponly(true); //default: false
$cookieOptions->setSameSite('Strict'); //default: Lax
```

```php
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;

/** @var Options $cookieOptions */
$cookie = new Cookie($cookieOptions);

$cookie->set('key', 'value<>', $ttl = true, $options = []);
$cookie->setRaw('key', 'value<>');
$cookie->delete('key');
$cookie->get('key');
$cookie->has('key');
```

## TTL (Time to left)

По-умолчанию ***true*** - срок действия cookie истечёт с окончанием сессии (при закрытии браузера)

Также может принимать другие значения:

- `(int) 0` `(bool) true` `(string) 'session'` - Срок действия cookie истечёт с окончанием сессии (при закрытии
  браузера)
- `(bool) false` - Cookie установлена не будет, либо будет удалена, если была установлена ранее
- `int|float (кроме 0)` - Положительное или отрицательно число, кроме 0, добавится к текущей дате количество секунд. При
  отрицательном значении cookie установлена не будет, либо будет удалена, если была установлена ранее
- `DateTime|DateTimeImmutable` - Установит ту дату которая указана в этом объекте.
- `string` - Разрешены относительные форматы даты/времени, которые понимает парсер функций strtotime(), DateTime и
  date_create(), например **'+1 week'**. Все относительно текущей даты.

