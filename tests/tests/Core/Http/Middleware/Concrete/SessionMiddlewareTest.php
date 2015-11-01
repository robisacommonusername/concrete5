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

            return [$request, $response];
        });

        $this->assertTrue($called, 'Next not called.');
    }

}
