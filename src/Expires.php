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
     * @param mixed $ttl
     * @throws Exception
     */
    public function __construct($ttl)
    {
        $this->setExpires($ttl);
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
        if ($ttl === 0 || $ttl === false || strtolower((string)$ttl) === 'session') {
            $this->expires =  0;
            return;
        }

        // Устанавливаем время жизни на год
        if ($ttl === true) {
            $ttl = 60 * 60 * 24 * 365;
        }

        // Если число то прибавляем значение к метке времени timestamp
        // Для установки сессионной куки надо использовать FALSE
        if (is_numeric($ttl)) {
            $this->expires =  time() + (int)$ttl;
            return;
        }

        if (is_string($ttl)) {
            if (false !== $returnTtl = strtotime($ttl)) {
                $this->expires =  $returnTtl;
                return;
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