<?php

declare(strict_types=1);

namespace Enjoys\Cookie;

class Cookie
{
    /**
     * @var Options
     */
    private Options $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public static function get(string $key): ?string
    {
        return (self::has($key)) ? $_COOKIE[$key] : null;
    }


    public static function has(string $key): bool
    {
        return array_key_exists($key, $_COOKIE);
    }


    /**
     * @param string $name
     * @throws Exception
     */
    public function delete(string $name): void
    {
        $this->set($name, '', '-1 day');
    }


    /**
     * @param string $key
     * @param string|null $value
     * @param bool|int|string $ttl
     * @param array<mixed> $addedOptions Ассоциативный массив (array), который может иметь любой из ключей: expires, path, domain, secure, httponly и samesite.
     * @return bool
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setcookie.php
     */
    public function set(string $key, ?string $value, $ttl = true, array $addedOptions = []): bool
    {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);
        return setcookie(...$setParams);
    }

    /**
     * Отправляет cookie без URL-кодирования значения
     * @param string $key
     * @param string|null $value
     * @param bool|int|string $ttl
     * @param array<mixed> $addedOptions
     * @return bool
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setrawcookie.php
     */
    public function setRaw(string $key, ?string $value, $ttl = true, array $addedOptions = []): bool
    {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);
        return setrawcookie(...$setParams);
    }

    /**
     * @param string $key
     * @param string|null $value
     * @param bool|int|string $ttl
     * @param array<mixed> $addedOptions
     * @return array<mixed>
     * @throws Exception
     */
    private function getSetParams(string $key, ?string $value, $ttl = true, array $addedOptions = []): array
    {
//        if (headers_sent($filename, $linenum)) {
//            throw new Exception(
//                sprintf(
//                    "Cookies are not set. Headers have already been sent to %s in line %s\n",
//                    $filename,
//                    $linenum
//                )
//            );
//        }

        if ($value !== null) {
            $expires = new Expires($ttl);
            $this->options->setExpires($expires->getExpires());
        }

        return [
            $key,
            (string)$value,
            $this->options->getOptions($addedOptions),
        ];
    }

}
