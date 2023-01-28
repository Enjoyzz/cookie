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

    public function get(string $key): ?string
    {
        /** @var string[] $cookie */
        $cookie = $this->options->getRequest()->getCookieParams();
        return $cookie[$key] ?? null;
    }


    public function has(string $key): bool
    {
        return array_key_exists($key, $this->options->getRequest()->getCookieParams());
    }


    /**
     * @throws Exception
     */
    public function delete(string $name): void
    {
        $this->set($name, '', '-1 day');
        /** @var string[] $cookie */
        $cookie = $this->options->getRequest()->getCookieParams();
        unset($cookie[$name]);
        $this->options->setRequest($this->options->getRequest()->withCookieParams($cookie));
    }


    /**
     * @param string $key
     * @param string|null $value
     * @param bool|int|string|\DateTimeInterface $ttl
     * @param array<string, int|string|bool> $addedOptions
     * @return bool
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setcookie.php
     */
    public function set(
        string $key,
        ?string $value,
        bool|int|string|\DateTimeInterface $ttl = true,
        array $addedOptions = []
    ): bool {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);

        /**
         * @psalm-suppress InvalidArgument, MixedArgument
         */
        if (setcookie(...$setParams)) {
            $this->options->setRequest(
                $this->options->getRequest()->withCookieParams([
                    $key => urlencode((string)$value)
                ])
            );
            return true;
        }
        return false;
    }

    /**
     * Отправляет cookie без URL-кодирования значения
     * @param string $key
     * @param string|null $value
     * @param bool|int|string|\DateTimeInterface $ttl
     * @param array<string, int|string|bool> $addedOptions
     * @return bool
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setrawcookie.php
     */
    public function setRaw(
        string $key,
        ?string $value,
        bool|int|string|\DateTimeInterface $ttl = true,
        array $addedOptions = []
    ): bool {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);

        /**
         * @psalm-suppress InvalidArgument, MixedArgument
         */
        if (setrawcookie(...$setParams)) {
            $this->options->setRequest(
                $this->options->getRequest()->withCookieParams([
                    $key => $value
                ])
            );
            return true;
        }


        return false;
    }

    /**
     * @param string $key
     * @param string|null $value
     * @param bool|int|string|\DateTimeInterface $ttl
     * @param array<string, int|string|bool> $addedOptions
     * @return array{string, string, array<string, int|string|bool>}
     * @throws Exception
     */
    private function getSetParams(
        string $key,
        ?string $value,
        bool|int|string|\DateTimeInterface $ttl = true,
        array $addedOptions = []
    ): array {
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
