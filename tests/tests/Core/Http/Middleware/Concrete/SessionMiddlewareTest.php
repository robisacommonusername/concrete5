<?php

namespace tests\Core\Http\Middleware\Concrete;

use Concrete\Core\Http\Middleware\Concrete\SessionMiddleware;
use Concrete\Core\Utility\IPAddress;
use Zend\Diactoros\Response;

class SessionMiddlewareTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test enabling session and attaching to the request.
     */
    public function testEnableSession()
    {
        $session = $this->getMock('\Symfony\Component\HttpFoundation\Session\Session');
        $container = $this->getMock('\Illuminate\Container\Container');
        $service = $this->getMock('\Concrete\Core\Permission\IPService');
        $service->method('getRequestIP')->willReturn($this->getMock('\Concrete\Core\Utility\IPAddress'));

        $middleware = new SessionMiddleware($container, $session, $service);
        $middleware->setDirection($middleware::DIRECTION_IN);

        $request = $this->getMock('\Psr\Http\Message\ServerRequestInterface');
        $request
            ->expects($this->atLeastOnce())
            ->method('withAttribute')
            ->with('session', $session)
            ->willReturn($request);

        $response = $this->getMock('\Psr\Http\Message\ResponseInterface');

        $called = false;
        $middleware($request, $response, function($request, $response) use (&$called) {
            $called = true;
        });

        $this->assertTrue($called, 'Next not called.');
    }

    /**
     * Test disabling session
     */
    public function testDisableSession()
    {
        $session = $this->getMock('\Symfony\Component\HttpFoundation\Session\Session');
        $session->expects($this->once())->method('save');

        $request = $this->getMock('\Psr\Http\Message\ServerRequestInterface');
        $response = $this->getMock('\Psr\Http\Message\ResponseInterface');
        $container = $this->getMock('\Illuminate\Container\Container');
        $service = $this->getMock('\Concrete\Core\Permission\IPService');

        $middleware = new SessionMiddleware($container, $session, $service);
        $middleware->setDirection($middleware::DIRECTION_OUT);

        $called = false;
        $middleware($request, $response, function($request, $response) use (&$called) {
            $called = true;
        });

        $this->assertTrue($called, 'Next not called.');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCenterFailure()
    {
        $session = $this->getMock('\Symfony\Component\HttpFoundation\Session\Session');
        $request = $this->getMock('\Psr\Http\Message\ServerRequestInterface');
        $response = $this->getMock('\Psr\Http\Message\ResponseInterface');
        $container = $this->getMock('\Illuminate\Container\Container');
        $service = $this->getMock('\Concrete\Core\Permission\IPService');

        $middleware = new SessionMiddleware($container, $session, $service);
        $middleware->setDirection($middleware::DIRECTION_NONE);

        $middleware($request, $response, function($request, $response) {});
    }

}
