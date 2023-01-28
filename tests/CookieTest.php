<?php

namespace Tests\Enjoys\Cookie;

use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;
use HttpSoft\ServerRequest\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class CookieTest extends TestCase
{

    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $this->request = ServerRequestCreator::createFromGlobals(cookie: [
            'installed_cookie' => 'value'
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetCookie()
    {
        $cookie = new Cookie(new Options($this->request));
        $this->assertSame(true, $cookie->setRaw('keyRaw', 'value<>'));
        $this->assertSame(true, $cookie->set('key', 'value<>'));
        $this->assertSame(true, $cookie->has('key'));
        $this->assertSame('value%3C%3E', $cookie->get('key'));
        $this->assertSame('value<>', $cookie->get('keyRaw'));
        $this->assertSame('value', $cookie->get('installed_cookie'));

        $cookie->delete('key');
        $this->assertSame(null, $cookie->get('key'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testDeleteCookie(){
        $request = $this->request->withCookieParams([
            'cookie_key' => 'cookie_value'
        ]);
        $cookie = new Cookie(new Options($request));
        $this->assertSame('cookie_value', $cookie->get('cookie_key'));
        $cookie->delete('cookie_key');
        $this->assertSame(null, $cookie->get('cookie_key'));

    }


    /**
     * @runInSeparateProcess
     */
    public function testSetCookieFailedByExpired(){
        $cookie = new Cookie(new Options($this->request));
        $this->assertSame(true, $cookie->set('key', 'value', false));
        $this->assertSame(true, $cookie->setRaw('keyRaw', 'value', false));
        $this->assertSame(null, $cookie->get('key'));
        $this->assertSame(null, $cookie->get('keyRaw'));

    }


}
