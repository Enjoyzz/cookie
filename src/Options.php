<?php

declare(strict_types=1);

namespace Enjoys\Cookie;

use HttpSoft\ServerRequest\ServerRequestCreator;
use Psr\Http\Message\ServerRequestInterface;

class Options
{

    /**
     * @var array<string, int|string|bool>
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
        /** @var null|string $SERVER_NAME */
        $SERVER_NAME = $this->request->getServerParams()['SERVER_NAME'] ?? null;
        /** @var null|string $HTTPS */
        $HTTPS = $this->request->getServerParams()['HTTPS'] ?? null;

        $domain = (($SERVER_NAME !== 'localhost') ? preg_replace(
                '#^www\.#',
                '',
                strtolower((string)$SERVER_NAME)
            ) : false) ?? false;

        $this->setDomain($domain);
        $this->setSecure(($HTTPS === 'on'));
    }


    /**
     * @param array<string, int|string|bool> $addedOptions
     * @return array<string, int|string|bool>
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

    public function setExpires(int $expires): void
    {
        $this->options['expires'] = $expires;
    }

    public function setDomain(bool|string $domain): void
    {
        $this->options['domain'] = $domain;
    }


    public function setPath(string $path): void
    {
        $this->options['path'] = $path;
    }

    public function setHttponly(bool $httpOnly): void
    {
        $this->options['httponly'] = $httpOnly;
    }

    public function setSecure(bool $secure): void
    {
        $this->options['secure'] = $secure;
    }

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
