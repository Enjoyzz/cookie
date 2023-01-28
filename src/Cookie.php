<?php

declare(strict_types=1);

namespace Enjoys\Cookie;

class Cookie
{
    public function __construct(private Options $options)
    {
    }

    public function get(string $key = null): null|array|string
    {
        /** @var string[] $cookie */
        $cookie = $this->options->getRequest()->getCookieParams();
        if ($key === null) {
            return $cookie;
        }

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
        if ($this->set($name, '', '-1 day')) {
            /** @var array<string, string> $cookie */
            $cookie = $this->options->getRequest()->getCookieParams();
            unset($cookie[$name]);
            $this->options->setRequest($this->options->getRequest()->withCookieParams($cookie));
        }
    }


    /**
     * @param array<string, int|string|bool> $addedOptions
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setcookie.php
     */
    public function set(
        string $key,
        string $value,
        bool|int|string|\DateTimeInterface $ttl = true,
        array $addedOptions = []
    ): bool {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);

        /**
         * @psalm-suppress InvalidArgument, MixedArgument
         */
        if (setcookie(...$setParams)) {
            if ($setParams[2]['expires'] < 0) {
                return true;
            }
            $this->options->setRequest(
                $this->options->getRequest()->withCookieParams(
                    array_merge(
                        $this->options->getRequest()->getCookieParams(),
                        [
                            $key => urlencode($value)
                        ]
                    )
                )
            );
            return true;
        }
        return false;
    }

    /**
     * Отправляет cookie без URL-кодирования значения
     * @param array<string, int|string|bool> $addedOptions
     * @throws Exception
     * @see https://www.php.net/manual/ru/function.setrawcookie.php
     */
    public function setRaw(
        string $key,
        string $value,
        bool|int|string|\DateTimeInterface $ttl = true,
        array $addedOptions = []
    ): bool {
        $setParams = $this->getSetParams($key, $value, $ttl, $addedOptions);

        /**
         * @psalm-suppress InvalidArgument, MixedArgument
         */
        if (setrawcookie(...$setParams)) {
            if ($setParams[2]['expires'] < 0) {
                return true;
            }
            $this->options->setRequest(
                $this->options->getRequest()->withCookieParams(
                    array_merge(
                        $this->options->getRequest()->getCookieParams(),
                        [
                            $key => $value
                        ]
                    )
                )
            );
            return true;
        }


        return false;
    }

    /**
     * @param array<string, int|string|bool> $addedOptions
     * @return array{string, string, array<string, int|string|bool>}
     * @throws Exception
     */
    private function getSetParams(
        string $key,
        string $value,
        bool|int|string|\DateTimeInterface $ttl,
        array $addedOptions = []
    ): array {
        $expires = new Expires($ttl);
        $this->options->setExpires($expires->getExpires());

        return [$key, $value, $this->options->getOptions($addedOptions)];
    }
}
