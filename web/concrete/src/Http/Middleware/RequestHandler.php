<?php
namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Middleware\Pipeline\RequestPipelineInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RequestHandler
 *
 * @package Concrete\Core\Http
 */
class RequestHandler
{

    /** @type RequestPipelineInterface */
    protected $pipeline;

    /** @type MiddlewareInterface[][] */
    protected $middlewares = [];

    /** @type MiddlewareInterface */
    protected $kernel;

    /**
     * RequestHandler constructor.
     *
     * @param RequestPipelineInterface $pipeline
     */
    public function __construct(RequestPipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @return MiddlewareInterface
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param MiddlewareInterface $kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return MiddlewareInterface[][]
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * @param MiddlewareInterface[][] $middlewares
     */
    public function setMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @return RequestPipelineInterface
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }

    /**
     * @param RequestPipelineInterface $pipeline
     */
    public function setPipeline($pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * Add a middleware to the stack
     *
     * @param MiddlewareInterface $middleware The middleware
     * @param int                 $priority   1 is first
     */
    public function addMiddleware(MiddlewareInterface $middleware, $priority = 100)
    {
        $this->middlewares[$priority][] = $middleware;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handleRequest(RequestInterface $request, ResponseInterface $response)
    {
        $handler = $this;
        $in = MiddlewareInterface::DIRECTION_IN;
        $out = MiddlewareInterface::DIRECTION_OUT;
        $kernel = $this->getKernel();

        $do_pipe = function($direction, \Closure $then) use ($request, $response, $handler) {
            return $handler->pipeRequest($direction, $request, $response, $then);
        };

        return $do_pipe($in, function($request, $response) use ($do_pipe, $out, $kernel) {
            $kernel->setDirection($kernel::DIRECTION_NONE);

            return $kernel->handleRequest($request, $response, function ($request, $response) use ($do_pipe, $out) {
                return $do_pipe($out, function($request, $response) {
                    return $response;
                });
            });
        });
    }

    /**
     * Send request and response through pipeline in a direction
     *
     * @param int                                 $direction MiddlewareInterface::DIRECTION_*
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Closure                            $then
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function pipeRequest($direction, RequestInterface $request, ResponseInterface $response, \Closure $then)
    {
        $middlewares_priorities = $this->middlewares;
        $pipeline = $this->pipeline;

        if ($direction === MiddlewareInterface::DIRECTION_OUT) {
            // We're going out, so lets send them through in the opposite direction
            $middlewares_priorities = array_reverse($middlewares_priorities);
        }

        $middlewares = [];
        foreach ($middlewares_priorities as $middleware_group) {
            foreach ($middleware_group as $middleware) {
                $middleware->setDirection($direction);
            }
            $middlewares = array_merge($middlewares, $middleware_group);
        }

        return $pipeline
            ->send($request, $response)
            ->through($middlewares)
            ->then(function($request, $response) use ($middlewares, $pipeline, $then) {
                return $then($request, $response);
            });
    }

}
