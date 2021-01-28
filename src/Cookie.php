<?php

declare(strict_types=1);

namespace Enjoys\Cookie;

use Enjoys\Http\ServerRequest;
use Enjoys\Http\ServerRequestInterface;

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
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setcookie.php
     */
    public function set(string $key, ?string $value, $ttl = true, array $addedOptions = []): void
    {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);
        setcookie(...$setParams);
    }

    /**
     * Отправляет cookie без URL-кодирования значения
     * @param string $key
     * @param string|null $value
     * @param bool|int|string $ttl
     * @param array<mixed> $addedOptions
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setrawcookie.php
     */
    public function setRaw(string $key, ?string $value, $ttl = true, array $addedOptions = []): void
    {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);
        setrawcookie(...$setParams);
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
        if (headers_sent($filename, $linenum)) {
            throw new Exception(
                sprintf(
                    "Cookies are not set. Headers have already been sent to %s in line %s\n",
                    $filename,
                    $linenum
                )
            );
        }

        if ($value !== null) {
            $expires = $this->getExpires($ttl);
            $this->options->setExpires($expires);
        }

        return [
            $key,
            (string)$value,
            $this->options->getOptions($addedOptions),
        ];
    }

    /**
     * @param mixed $ttl
     * @return int
     * @throws Exception
     * @see http://php.net/manual/ru/datetime.formats.relative.php
     */
    private function getExpires($ttl): int
    {
        //Срок действия cookie истечет с окончанием сессии (при закрытии браузера).
        if ($ttl === 0 || $ttl === false || strtolower((string)$ttl) === 'session') {
            return 0;
        }

        // Устанавливаем время жизни на год
        if ($ttl === true) {
            $ttl = 60 * 60 * 24 * 365;
        }

        // Если число то прибавляем значение к метке времени timestamp
        // Для установки сессионной куки надо использовать FALSE
        if (is_numeric($ttl)) {
            return time() + (int)$ttl;
        }

        if (is_string($ttl)) {
            if (false !== $returnTtl = strtotime($ttl)) {
                return $returnTtl;
            }
            throw new Exception(sprintf('strtotime() failed to convert string "%s" to timestamp', $ttl));
        }
    }
}
