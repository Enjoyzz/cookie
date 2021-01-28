<?php
declare(strict_types=1);

namespace Enjoys\Cookie;


class Expires
{
    /**
     * @var int
     */
    private int $expires = -1;
    private ?int $currentTimestamp;

    /**
     * Expires constructor.
     * @param mixed $ttl
     * @param int|null $currentTimestamp
     * @throws Exception
     */
    public function __construct($ttl, int $currentTimestamp = null)
    {
        $this->currentTimestamp = $currentTimestamp ?? time();
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
            $this->expires =  $this->currentTimestamp + (int)$ttl;
            return;
        }

        if (is_string($ttl)) {
            if (false !== $returnTtl = strtotime($ttl, $this->currentTimestamp)) {
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