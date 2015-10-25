<?php

namespace Concrete\Core\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ClosureMiddleware
 * A middleware that delegates handling to a closure
 * @package Concrete\Core\Http\Middleware
 */
class ClosureMiddleware implements MiddlewareInterface
{

    use MiddlewareTrait;

    /**
     * @type \Closure The handleRequest closure
     */
    protected $closure;

    /**
     * ClosureMiddleware constructor.
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Handle a request and a response
     * This method will either return $next($request, $response); or will create and return an error response like a 404
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Closure $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handleRequest(ServerRequestInterface $request, ResponseInterface $response, \Closure $next)
    {
        $closure = $this->closure;
        return $closure($this, $request, $response, $next);
    }

}
