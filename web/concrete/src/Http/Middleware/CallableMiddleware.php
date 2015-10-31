<?php

namespace Concrete\Core\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ClosureMiddleware
 * A middleware that delegates handling to a closure
 * @package Concrete\Core\Http\Middleware
 */
class CallableMiddleware implements MiddlewareInterface
{

    use MiddlewareTrait;

    /**
     * @type callable The handleRequest closure
     */
    protected $closure;

    /**
     * CallableMiddleware constructor.
     * @param callable $closure
     */
    public function __construct(callable $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Handle a request and a response
     * This method will either return $next($request, $response); or will create and return an error response like a 404
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $closure = $this->closure;
        return $closure($this, $request, $response, $next);
    }

}
