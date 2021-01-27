<?php

declare(strict_types=1);

namespace Enjoys\Cookie;

use Enjoys\Http\ServerRequest;
use Enjoys\Http\ServerRequestInterface;

class Cookie
{

    /**
     * @var array<mixed>
     */
    private array $options = [
        'expires' => 0,
        'path' => '',
        'domain' => false,
        'secure' => false,
        'httponly' => false,
        'samesite' => 'Lax',

    ];

    /**
     * @var ServerRequestInterface
     */
    private ServerRequestInterface $serverRequest;

    public function __construct(ServerRequestInterface $serverRequest = null)
    {
        $this->serverRequest = $serverRequest ?? new ServerRequest();
        $this->setDomain();
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
     * @param array<mixed> $options Ассоциативный массив (array), который может иметь любой из ключей: expires, path, domain, secure, httponly и samesite.
     * @param bool $raw Отправляет cookie без URL-кодирования значения
     * @throws Exception
     */
    public function set(string $key, ?string $value, $ttl = true, array $options = [], $raw = false): void
    {
        if (headers_sent($filename, $linenum)) {
            throw new Exception(
                sprintf(
                    "Куки не установлены\nЗаголовки уже были отправлены в %s в строке %s\n",
                    $filename,
                    $linenum
                )
            );
        }

        //Если $value равно NULL, то удаляем эту куку
        if ($value === null) {
            $ttl = '-1 day';
        }

        $this->setExpires($ttl);

        if($raw === true){
            setrawcookie($key, (string)$value, $this->mergeOptions($options));
            return;
        }
        setcookie($key, (string)$value, $this->mergeOptions($options));
    }

    /**
     * @param array<mixed> $options
     * @return array<mixed>
     */
    private function mergeOptions(array $options): array
    {
        $mergedOptions = $this->options;
        foreach ($options as $key => $option) {
            if (isset($this->options[$key])) {
                $mergedOptions[$key] = $option;
            }
        }

        return $mergedOptions;
    }

    /**
     * @param mixed $ttl
     * @return void
     * @throws Exception
     * @see http://php.net/manual/ru/datetime.formats.relative.php
     */
    private function setExpires($ttl): void
    {
        //Срок действия cookie истечет с окончанием сессии (при закрытии браузера).
        if ($ttl === false || strtolower((string)$ttl) === 'session') {
            $this->options['expires'] = 0;
            return;
        }


        // Если число то прибавляем значение к метке времени timestamp
        // Для установки сессионной куки надо использовать FALSE
        if (is_numeric($ttl)) {
            $this->options['expires'] = time() + (int)$ttl;
            return;
        }


        // Устанавливаем время жизни на год
        if ($ttl === true) {
            $ttl = '+1 year';
        }

        if (is_string($ttl)) {
            if (false !== $returnTtl = strtotime($ttl)) {
                $this->options['expires'] = $returnTtl;
                return;
            }
            throw new Exception(sprintf('strtotime() failed to convert string "%s" to timestamp', $ttl));
        }
//        $this->options['expires'] = (int)$ttl;
    }


    /**
     * @param false|string $domain
     */
    public function setDomain($domain = false): void
    {
        if ($domain === false) {
            if ($this->serverRequest->server('SERVER_NAME') != 'localhost') {
                $domain = (preg_replace(
                    '#^www\.#',
                    '',
                    strtolower((string)$this->serverRequest->server('SERVER_NAME'))
                ));
            }
        }

        $this->options['domain'] = $domain ?? false;
        $this->options['secure'] = ($this->serverRequest->server('HTTPS') == 'on');
    }


    public function setPath(string $path): void
    {
        $this->options['path'] = $path;
    }

    /**
     * @param bool $httpOnly
     */
    public function setHttponly(bool $httpOnly): void
    {
        $this->options['httponly'] = $httpOnly;
    }

    /**
     * @param bool $secure
     */
    public function setSecure(bool $secure): void
    {
        $this->options['secure'] = $secure;
    }

    /**
     * @param string $sameSite
     */
    public function setSameSite(string $sameSite): void
    {
        $this->options['samesite'] = $sameSite;
    }
}
