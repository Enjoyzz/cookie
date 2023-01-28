<?php

namespace Tests\Enjoys\Cookie;

use Enjoys\Cookie\Exception;
use Enjoys\Cookie\Expires;
use PHPUnit\Framework\TestCase;

class ExpiresTest extends TestCase
{

    private $currentTimestamp = 1;

    /**
     * @dataProvider expiresData
     */
    public function testGetExpires($ttl, $expect)
    {
        $expires = new Expires($ttl, $this->currentTimestamp);
        $this->assertSame($expect, $expires->getExpires());
    }

    public function expiresData(): array
    {
        return [
            [0, 0],
            [false, -1],
            ['session', 0],
            ['Session', 0],
            ['sessioN', 0],
            ['SeSsiOn', 0],
            [true, 0],
            [10, $this->currentTimestamp + 10],
            [-1, $this->currentTimestamp + -1],
            ['10', $this->currentTimestamp + 10],
            ['+1 year', $this->currentTimestamp + (60 * 60 * 24 * 365)],
            ['-1 year', $this->currentTimestamp + (-60 * 60 * 24 * 365)],
            ['+1 month', $this->currentTimestamp + (60 * 60 * 24 * 31)],
            ['-1 month', $this->currentTimestamp + (-60 * 60 * 24 * 31)],
            ['1 day', $this->currentTimestamp + (60 * 60 * 24)],
            ['-1 day', $this->currentTimestamp + (-60 * 60 * 24)],
            [new \DateTime('1970-01-02'), 3600 * 24],
            [new \DateTimeImmutable('1970-01-05'), 4 * 3600 * 24],
            [new \DateTimeImmutable('@404'), 404],
            [(new \DateTimeImmutable('@0'))->modify('1 hour'), 3600],
            [(new \DateTime('@0'))->modify('1 minute'), 60],
        ];
    }

    public function testException()
    {
        $this->expectException(Exception::class);
        new Expires('invalid string');
    }
}
