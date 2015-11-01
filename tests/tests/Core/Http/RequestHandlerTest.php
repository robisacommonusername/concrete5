<?php

namespace tests\Core\Http;

use Concrete\Core\Http\Middleware\CallableMiddleware;
use Concrete\Core\Http\Middleware\Pipeline\MiddlewarePipeline;
use Concrete\Core\Http\RequestHandler;

class RequestHandlerTest extends \PHPUnit_Framework_TestCase
{

    public function testMiddlewareOrder()
    {
        $order = 0;
        $call_order = [];

        $make_middleware = function($id) use (&$order, &$call_order) {
            return function($request, $response, $next) use ($id, &$order, &$call_order) {
                $order++;
                $call_order[] = "{$id} {$order} in";

                $out = $next($request, $response);

                $order++;
                $call_order[] = "{$id} {$order} out";

                return $out;
            };
        };

        // First middleware
        $middleware1 = $make_middleware('first');
        $middleware2 = $make_middleware('second');
        $middleware3 = $make_middleware('third');

        $request_handler = new RequestHandler(new MiddlewarePipeline());
        $request_handler->setMiddlewares([[$middleware1, $middleware2], [$middleware3]]);

        $request_handler->handleRequest(
            $request = $this->getMock('\Psr\Http\Message\ServerRequestInterface'),
            $response = $this->getMock('\Psr\Http\Message\ResponseInterface'));

        $this->assertEquals([
            'first 1 in',
            'second 2 in',
            'third 3 in',
            'third 4 out',
            'second 5 out',
            'first 6 out'
        ], $call_order);

        $call_order = [];
        $request_handler->handleRequest($request, $response);

        $this->assertEquals([
            'first 7 in',
            'second 8 in',
            'third 9 in',
            'third 10 out',
            'second 11 out',
            'first 12 out'
        ], $call_order);
    }

    public function testAnonymousMiddleware()
    {
        $called = 0;
        $middleware = function($request, $response, $next) use (&$called) {
            $called++;
            return $next($request, $response);
        };

        $handler = new RequestHandler(new MiddlewarePipeline());
        $handler->addMiddleware($middleware);
        $handler->handleRequest(
            $request = $this->getMock('\Psr\Http\Message\ServerRequestInterface'),
            $response = $this->getMock('\Psr\Http\Message\ResponseInterface'));

        $this->assertEquals(1, $called);
    }

    public function testSetters()
    {
        $request_handler = new RequestHandler(new MiddlewarePipeline());
        $request_handler->setMiddlewares($array = new \ArrayObject(range(100, 200)));

        $this->assertEquals($array, $request_handler->getMiddlewares());

        $request_handler = new RequestHandler(new MiddlewarePipeline());
        $request_handler->setPipeline($pipeline = $this->getMock('\Concrete\Core\Http\Middleware\Pipeline\MiddlewarePipeline'));

        $this->assertEquals($pipeline, $request_handler->getPipeline());
    }

}
