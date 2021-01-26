<?php

declare(strict_types=1);

namespace Enjoys\Cookie;


use Enjoys\Http\ServerRequest;
use Enjoys\Http\ServerRequestInterface;

class Cookie
{
    const TTL_DEFAULT = '+1 year';

    /**
     * @var string|false
     */
    private $domain = 'localhost';
    private string $path = '';
    private bool $secure = false;
    private bool $httpOnly = false;
    private string $sameSite = '';
    /**
     * @var ServerRequestInterface
     */
    private ?ServerRequestInterface $serverRequest;

    public function __construct(ServerRequestInterface $serverRequest = null)
    {
        $this->serverRequest = $serverRequest ?? new ServerRequest();
        $this->setDomain();
    }

    /**
     * @param string $key
     * @param string|null $value
     * @param bool|int|string $ttl
     * @param bool $httponly
     * @throws \Exception
     */
    public function set(string $key, ?string $value, $ttl = Cookie::TTL_DEFAULT, bool $httponly = false): void
    {
        //Если $value равно NULL, то удаляем эту куку
        if ($value === null) {
            $ttl = '-1 day';
        }


        if (headers_sent($filename, $linenum)) {
            throw new Exception(
                sprintf(
                    "Куки не установлены\nЗаголовки уже были отправлены в %s в строке %s\n",
                    $filename,
                    $linenum
                )
            );
        }

        setcookie(
            $key,
            $value,
            [
                'expires' => $this->getExpires($ttl),
                'path' => $this->getPath(),
                'domain' => $this->getDomain(),
                'secure' => $this->isSecure(),
                'httponly' => $this->isHttpOnly(),
                'samesite' => $this->getSameSite()
            ]
        );
    }

    /**
     * @param bool|int|string $ttl
     * @return int
     * @throws Exception
     * @see http://php.net/manual/ru/datetime.formats.relative.php
     */
    private function getExpires($ttl): int
    {
        //Срок действия cookie истечет с окончанием сессии (при закрытии браузера).
        if ($ttl === false || strtolower((string)$ttl) === 'session') {
            return 0;
        }


        // Если число то прибавляем значение к метке времени timestamp
        // Для установки сессионной куки надо использовать FALSE
        if (is_numeric($ttl)) {
            return time() + (int)$ttl;
        }


        // Устанавливаем время жизни по константе Cookie::TTL_DEFAULT
        if ($ttl === true) {
            $ttl = Cookie::TTL_DEFAULT;
        }


        if (is_string($ttl)) {
            if (false !== $returnTtl = strtotime($ttl)) {
                return $returnTtl;
            }
            throw new Exception(sprintf('strtotime() failed to convert string "%s" to timestamp', $ttl));
        }

        return (int)$ttl;
    }


    public static function get(string $key): ?string
    {
        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : null;
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
     * @return false|string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param false|string $domain
     */
    public function setDomain($domain = false): void
    {
        $domain = ($this->serverRequest->server('SERVER_NAME') != 'localhost') ? preg_replace(
            '#^www\.#',
            '',
            strtolower(
                $_SERVER['SERVER_NAME']
            )
        ) : false;


        $this->domain = $domain;
        $this->secure = (isset($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS']) == 'on');
    }


    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }


    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @param bool $httpOnly
     */
    public function setHttpOnly(bool $httpOnly): void
    {
        $this->httpOnly = $httpOnly;
    }

    /**
     * @param bool $secure
     */
    public function setSecure(bool $secure): void
    {
        $this->secure = $secure;
    }

    /**
     * @return string
     */
    public function getSameSite(): string
    {
        return $this->sameSite;
    }

    /**
     * @param string $sameSite
     */
    public function setSameSite(string $sameSite): void
    {
        $this->sameSite = $sameSite;
    }

}