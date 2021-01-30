<?php

namespace Tests\Enjoys\Cookie;

use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;
use PHPUnit\Framework\TestCase;


class CookieTest extends TestCase
{


    /**
     * @runInSeparateProcess
     */
    public function test1()
    {
        $cookie = new Cookie(new Options());

        $cookieMock = $this->getMockBuilder(Cookie::class)->disableOriginalConstructor()->getMock();
        $cookieMock->expects($this->any())->method('set')->willReturnCallback(
            function ($key, $value) use ($cookie) {
                $cookie->set($key, $value, 3600);
                $_COOKIE[$key] = urlencode($value);
                return true;
            }
        );

        $cookieMock->expects($this->any())->method('setRaw')->willReturnCallback(
            function ($key, $value) use ($cookie) {
                $cookie->setRaw($key, $value, 3600);
                $_COOKIE[$key] = $value;
                return true;
            }
        );

        $cookieMock->expects($this->any())->method('delete')->willReturnCallback(
            function ($key) use ($cookie) {
                $cookie->delete($key);
                unset($_COOKIE[$key]);
            }
        );

        $cookieMock->setRaw('keyRaw', 'value<>');
        $cookieMock->set('key', 'value<>');

        $this->assertSame(true, Cookie::has('key'));
        $this->assertSame('value%3C%3E', Cookie::get('key'));
        $this->assertSame('value<>', Cookie::get('keyRaw'));
        $cookieMock->delete('key');
        $this->assertSame(false, Cookie::has('key'));

    }

}
