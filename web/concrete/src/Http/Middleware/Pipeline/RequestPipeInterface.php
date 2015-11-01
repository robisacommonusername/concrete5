<?php
namespace Concrete\Core\Http\Middleware\Pipeline;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestPipeInterface
{

    /**
     * Handle a request and a response
     * This method will either return $next($request, $response); or will create and return an error response like a 404
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     * @return array [$request, $response]
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next);

}
