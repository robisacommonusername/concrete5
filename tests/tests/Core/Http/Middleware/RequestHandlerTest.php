<?php

namespace tests\Core\Http\Middleware;

use Concrete\Core\Http\Middleware\ClosureMiddleware;
use Concrete\Core\Http\Middleware\Pipeline\MiddlewarePipeline;
use Concrete\Core\Http\Middleware\RequestHandler;

class RequestHandlerTest extends \PHPUnit_Framework_TestCase
{

    public function testMiddlewareOrder()
    {
        $order = 0;
        $call_order = [];

        // First middleware
        $middleware1 = new ClosureMiddleware(function(
            ClosureMiddleware $middlware, $request, $response, $next) use (&$order, &$call_order) {
            $order++;

            $call_order[] = "first {$order} {$middlware->getDirection()}";
            return $next($request, $response);
        });

        // Second middleware
        $middleware2 = new ClosureMiddleware(function(
            ClosureMiddleware $middlware, $request, $response, $next) use (&$order, &$call_order) {
            $order++;

            $call_order[] = "second {$order} {$middlware->getDirection()}";
            return $next($request, $response);
        });

        // Router
        $router = new ClosureMiddleware(function(
            ClosureMiddleware $middlware, $request, $response, $next) use (&$order, &$call_order) {
            $order++;

            $call_order[] = "router {$order} {$middlware->getDirection()}";
            return $next($request, $response);
        });

        $request_handler = new RequestHandler(new MiddlewarePipeline());
        $request_handler->addMiddleware($middleware1, 1);
        $request_handler->addMiddleware($middleware2, 2);
        $request_handler->setRouter($router);

        $request_handler->handleRequest(
            $request = $this->getMock('\Psr\Http\Message\ServerRequestInterface'),
            $response = $this->getMock('\Psr\Http\Message\ResponseInterface'));

        $this->assertEquals([
            'first 1 1',
            'second 2 1',
            'router 3 0',
            'second 4 2',
            'first 5 2'
        ], $call_order);

        $order = 0;
        $call_order = [];
        $request_handler->setRouter(null);

        $request_handler->handleRequest($request, $response);

        $this->assertEquals([
            'first 1 1',
            'second 2 1',
            'second 3 2',
            'first 4 2'
        ], $call_order);

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
