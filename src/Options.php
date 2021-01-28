<?php

namespace Enjoys\Cookie;

use Enjoys\Http\ServerRequest;
use Enjoys\Http\ServerRequestInterface;

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
        'expires',
        'path',
        'domain',
        'secure',
        'httponly',
        'samesite'
    ];

    public function __construct(ServerRequestInterface $serverRequest = null)
    {
        $serverRequest ??= new ServerRequest();

        $domain = (($serverRequest->server('SERVER_NAME') != 'localhost') ? preg_replace(
            '#^www\.#',
            '',
            strtolower((string)$serverRequest->server('SERVER_NAME'))
        ) : false) ?? false;

        $this->setDomain($domain);
        $this->setSecure(($serverRequest->server('HTTPS') == 'on'));
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
}
