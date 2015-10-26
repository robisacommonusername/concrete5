<?php

namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Events;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class DispatcherMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use MiddlewareTrait, ApplicationAwareTrait;

    /** @type \Concrete\Core\Http\Middleware\Concrete\HttpFoundationFactory  */
    protected $request_factory;

    /** @type \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory */
    protected $response_factory;

    /**
     * CacheMiddleware constructor.
     *
     * @param \Concrete\Core\Application\Application $application
     * @param \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory $request_factory
     * @param \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory $response_factory
     */
    public function __construct(Application $application, HttpFoundationFactory $request_factory, DiactorosFactory $response_factory)
    {
        $this->setApplication($application);
        $this->request_factory = $request_factory;
        $this->response_factory = $response_factory;
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
        $app = $this->getApplication();
        if ($this->getDirection() == $this::DIRECTION_NONE && $app instanceof Application) {
            $symfony_request = $this->request_factory->createRequest($request);

            Request::setInstance($symfony_request);
            Events::dispatch('on_before_dispatch');
            $dispatcher_response = $app->dispatch($symfony_request);
            $response = $this->response_factory->createResponse($dispatcher_response);
        }

        return $next($request, $response);
    }

}
