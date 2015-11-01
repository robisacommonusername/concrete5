<?php
namespace Concrete\Core\Http;

use Concrete\Core\Http\Middleware\Pipeline\RequestPipelineInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RequestHandler
 *
 * @package Concrete\Core\Http
 */
interface RequestHandlerInterface
{

    /**
     * Get a list of middlewares [$priority => [ $middleware_1, $middleware2 ]];
     *
     * @return MiddlewareInterface[][]|callable[][]
     */
    public function getMiddlewares();

    /**
     * Set the list of middlewares
     *
     * @param MiddlewareInterface[][]|callable[][] $middlewares
     */
    public function setMiddlewares($middlewares);

    /**
     * Get the pipeline
     *
     * @return RequestPipelineInterface
     */
    public function getPipeline();

    /**
     * Set the pipeline
     *
     * @param RequestPipelineInterface $pipeline
     */
    public function setPipeline($pipeline);

    /**
     * Add a middleware to the stack
     *
     * @param MiddlewareInterface|callable $middleware The middleware
     * @param int $priority 1 is first
     */
    public function addMiddleware(callable $middleware, $priority = 100);

    /**
     * Handle a request
     * If a `$then` is provided, it MUST return [ RequestInterface $request, ResponseInterface $response ];
     *
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param callable                            $then function($request, $response): [$request, $response];
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handleRequest(RequestInterface $request, ResponseInterface $response, callable $then);

}
