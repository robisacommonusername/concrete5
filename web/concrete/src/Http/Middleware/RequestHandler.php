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
class RequestHandler implements RequestHandlerInterface
{

    /** @type RequestPipelineInterface */
    protected $pipeline;

    /** @type callable[][]|MiddlewareInterface[][] */
    protected $middlewares = [];

    /** @type callable|MiddlewareInterface */
    protected $router;

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
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setRouter($router)
    {
        $this->router = $router;
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
    public function addMiddleware(callable $middleware, $priority = 100)
    {
        $this->middlewares[$priority][] = $middleware;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, ResponseInterface $response, callable $after = null)
    {
        $handler = $this;
        $in = MiddlewareInterface::DIRECTION_IN;
        $out = MiddlewareInterface::DIRECTION_OUT;
        $router = $this->getRouter();

        $do_pipe = function($direction, callable $then) use ($request, $response, $handler) {
            return $handler->pipeRequest($direction, $request, $response, $then);
        };

        return $do_pipe($in, function($request, $response) use ($do_pipe, $out, $router, $after, $handler) {
            if ($router) {
                if ($router instanceof MiddlewareInterface) {
                    $router->setDirection($router::DIRECTION_NONE);
                }

                return $router($request, $response, function ($request, $response) use ($do_pipe, $out, $after, $handler) {
                    return $handler->pipeRequest($out, $request, $response, function($request, $response) use ($after) {
                        if ($after) {
                            return $after($request, $response);
                        } else {
                            return $response;
                        }
                    });
                });
            }

            return $do_pipe($out, function($request, $response) use ($after) {
                if ($after) {
                    return $after($request, $response);
                } else {
                    return $response;
                }
            });
        });
    }

    /**
     * Send request and response through pipeline in a direction
     *
     * @param int                                 $direction MiddlewareInterface::DIRECTION_*
     * @param \Psr\Http\Message\RequestInterface  $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param callable                            $then
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function pipeRequest($direction, RequestInterface $request, ResponseInterface $response, callable $then)
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

                if ($middleware instanceof MiddlewareInterface) {
                    $middleware->setDirection($direction);
                }
            }
            $middlewares = array_merge($middlewares, $middleware_group);
        }

        return $pipeline
            ->send($request, $response)
            ->through($middlewares)
            ->then(function($request, $response) use ($middlewares, $pipeline, $then) {
                $response = $then($request, $response);
                return $response;
            });
    }

}
