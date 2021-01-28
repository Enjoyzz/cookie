<?php


namespace Enjoys\Cookie;


class Expires
{
    /**
     * @var int
     */
    private int $expires = -1;

    /**
     * Expires constructor.
     * @param int|string|bool $ttl
     * @throws Exception
     */
    public function __construct($ttl)
    {
        $this->expires = $this->convertToInt($ttl);
    }

    /**
     * @param int|string|bool $ttl
     * @return int
     * @throws Exception
     * @see http://php.net/manual/ru/datetime.formats.relative.php
     */
    private function convertToInt($ttl): int
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

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }
}