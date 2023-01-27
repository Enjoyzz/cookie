<?php

namespace Tests\Enjoys\Cookie;

use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;
use HttpSoft\Message\RequestFactory;
use HttpSoft\ServerRequest\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;


class CookieTest extends TestCase
{

    private  ServerRequestInterface  $request;

    protected function setUp(): void
    {
        $this->request = ServerRequestCreator::create();
    }

    /**
     * @runInSeparateProcess
     */
    public function test1()
    {

        $cookie = new Cookie(new Options($this->request));

        $cookieMock = $this->getMockBuilder(Cookie::class)->disableOriginalConstructor()->getMock();
        $cookieMock->expects($this->any())->method('set')->willReturnCallback(
            function ($key, $value) use ($cookie) {
                $cookie->set($key, $value, 3600);
                return true;
            }
        );

        $cookieMock->expects($this->any())->method('setRaw')->willReturnCallback(
            function ($key, $value) use ($cookie) {
                $cookie->setRaw($key, $value, 3600);
                return true;
            }
        );

        $cookieMock->expects($this->any())->method('delete')->willReturnCallback(
            function ($key) use ($cookie) {
                $cookie->delete($key);
            }
        );

        $cookieMock->setRaw('keyRaw', 'value<>');
        $cookieMock->set('key', 'value<>');

        $this->assertSame(true, $cookie->has('key'));
        $this->assertSame('value%3C%3E', $cookie->get('key'));
        $this->assertSame('value<>', $cookie->get('keyRaw'));
        $cookieMock->delete('key');
        $this->assertSame(false,$cookie->get('key'));

    }

}
