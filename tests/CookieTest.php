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
        $this->request = ServerRequestCreator::create();
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetCookie()
    {
        $cookie = new Cookie(new Options($this->request));

        $cookie->setRaw('keyRaw', 'value<>');
        $this->assertSame('value<>', $cookie->get('keyRaw'));

        $cookie->set('key', 'value<>');
        $this->assertSame(true, $cookie->has('key'));
        $this->assertSame('value%3C%3E', $cookie->get('key'));

        $cookie->delete('key');
        $this->assertSame(null, $cookie->get('key'));
    }

}
