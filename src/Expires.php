<?php

declare(strict_types=1);

namespace Enjoys\Cookie;

class Expires
{
    private int $expires = -1;
    private int $currentTimestamp;

    /**
     * @throws Exception
     */
    public function __construct(bool|int|string|\DateTimeInterface $ttl, ?int $currentTimestamp = null)
    {
        $this->currentTimestamp = $currentTimestamp ?? time();
        $this->setExpires($ttl);
    }

    /**
     * @throws Exception
     * @see http://php.net/manual/ru/datetime.formats.relative.php
     */
    private function setExpires(bool|int|string|\DateTimeInterface $ttl): void
    {
        if ($ttl instanceof \DateTimeInterface) {
            $this->expires = $ttl->getTimestamp();
            return;
        }

        //Срок действия cookie истечет с окончанием сессии (при закрытии браузера).
        if ($ttl === 0 || $ttl === true || strtolower((string)$ttl) === 'session') {
            $this->expires = 0;
            return;
        }

        // Если число, то прибавляем значение к метке времени timestamp
        // Для установки сессионной куки надо использовать FALSE
        if (is_numeric($ttl)) {
            $this->expires = $this->currentTimestamp + (int)$ttl;
            return;
        }

        if (is_string($ttl)) {
            if (false !== $returnTtl = strtotime($ttl, $this->currentTimestamp)) {
                $this->expires = $returnTtl;
                return;
            }
            throw new Exception(sprintf('strtotime() failed to convert string "%s" to timestamp', $ttl));
        }
    }


    public function getExpires(): int
    {
        return $this->expires;
    }
}
