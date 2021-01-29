<?php

namespace Tests\Enjoys\Cookie;

use Enjoys\Cookie\Options;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{

    public function testSetHttponly()
    {
        $options = new Options();
        $this->assertSame(false, $options->getOptions()['httponly']);
        $options->setHttponly(true);
        $this->assertSame(true, $options->getOptions()['httponly']);
    }

    public function testSetDomain()
    {
        $options = new Options();
        $options->setDomain(false);
        $this->assertSame(false, $options->getOptions()['domain']);
        $options->setDomain('domain.com');
        $this->assertSame('domain.com', $options->getOptions()['domain']);
    }

    public function testSetSameSite()
    {
        $options = new Options();
        $this->assertArrayNotHasKey('samesite', $options->getOptions());
        $options->setSameSite('Lax');
        $this->assertSame('Lax', $options->getOptions()['samesite']);
    }

    public function testSetPath()
    {
        $options = new Options();
        $this->assertSame('', $options->getOptions()['path']);
        $options->setPath('/');
        $this->assertSame('/', $options->getOptions()['path']);
    }

    public function testSetSecure()
    {
        $options = new Options();
        $this->assertSame(false, $options->getOptions()['secure']);
        $options->setSecure(true);
        $this->assertSame(true, $options->getOptions()['secure']);
    }

    public function testSetExpires()
    {
        $options = new Options();
        $this->assertSame(-1, $options->getOptions()['expires']);
        $options->setExpires(100500);
        $this->assertSame(100500, $options->getOptions()['expires']);
    }

    public function testGetOptionsWithAddedCustomOptions()
    {
        $options = new Options();
        $options->setSecure(true);
        $this->assertSame(false, $options->getOptions(['secure' => false])['secure']);

        $options->setPath('/');
        $this->assertSame('/path', $options->getOptions(['path' => '/path'])['path']);

        $options->setHttponly(true);
        $this->assertSame(false, $options->getOptions(['httponly' => false])['httponly']);

        $options->setSameSite('Strict');
        $this->assertSame('Lax', $options->getOptions(['samesite' => 'Lax'])['samesite']);

        $options->setDomain(false);
        $this->assertSame('example.com', $options->getOptions(['domain' => 'example.com'])['domain']);

        $options->setExpires(100500);
        $this->assertSame(100500, $options->getOptions(['expires' => 0])['expires']);
    }

}
