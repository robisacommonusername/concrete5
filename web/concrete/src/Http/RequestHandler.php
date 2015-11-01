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
class RequestHandler implements RequestHandlerInterface
{

    /** @type RequestPipelineInterface */
    protected $pipeline;

    /** @type callable[][]|MiddlewareInterface[][] */
    protected $middlewares = [];

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
     * {@inheritdoc}
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * {@inheritdoc}
     */
    public function setMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * {@inheritdoc}
     */
    public function addMiddleware(callable $middleware, $priority = 100)
    {
        $this->middlewares[$priority][] = $middleware;
    }

    /**
     * {@inheritdoc}
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }

    /**
     * {@inheritdoc}
     */
    public function setPipeline($pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, ResponseInterface $response, callable $then = null)
    {
       return $this->pipeRequest($request, $response, function($request, $response) use ($then) {
            if ($then) {
                return $then($request, $response);
            }

            return [ $request, $response ];
        });
    }

    /**
     * Send request and response through pipeline in a direction
     *
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param callable                            $then
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function pipeRequest(RequestInterface $request, ResponseInterface $response, callable $then)
    {
        $middlewares_priorities = $this->middlewares;
        $pipeline = $this->pipeline;

        $middlewares = [];
        foreach ($middlewares_priorities as $middleware_group) {
            $middlewares = array_merge($middlewares, $middleware_group);
        }

        return $pipeline
            ->send($request, $response)
            ->through($middlewares)
            ->then($then);
    }

}
