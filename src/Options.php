<?php

declare(strict_types=1);

namespace Enjoys\Cookie;

use HttpSoft\ServerRequest\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Options
 * @package Enjoys\Cookie
 */
class Options
{
    /**
     * @var array<mixed>
     */
    private array $options = [
        'expires' => -1,
        'path' => '',
        'domain' => false,
        'secure' => false,
        'httponly' => false,
    ];

    private const ALLOWED_OPTIONS = [
        'path',
        'domain',
        'secure',
        'httponly',
        'samesite'
    ];

    public function __construct(private ServerRequestInterface $request)
    {
        $SERVER_NAME = $this->request->getServerParams()['SERVER_NAME'] ?? null;
        $HTTPS = $this->request->getServerParams()['HTTPS'] ?? null;

        $domain = (($SERVER_NAME != 'localhost') ? preg_replace(
                '#^www\.#',
                '',
                strtolower((string)$SERVER_NAME)
            ) : false) ?? false;

        $this->setDomain($domain);
        $this->setSecure(($HTTPS == 'on'));
    }

    /**
     * @param array<mixed> $addedOptions
     * @return array<mixed>
     */
    public function getOptions(array $addedOptions = []): array
    {
        $options = $this->options;
        foreach ($addedOptions as $key => $option) {
            if (in_array($key, self::ALLOWED_OPTIONS)) {
                $options[$key] = $option;
            }
        }
        return $options;
    }

    /**
     * @param int $expires
     */
    public function setExpires(int $expires): void
    {
        $this->options['expires'] = $expires;
    }

    /**
     * @param false|string $domain
     */
    public function setDomain($domain): void
    {
        $this->options['domain'] = $domain;
    }


    /**
     * @param string $path
     */
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


    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }


    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
