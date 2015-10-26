<?php

namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class CacheMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use MiddlewareTrait, ApplicationAwareTrait;

    /** @type \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory */
    protected $request_factory;

    /** @type \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory */
    protected $response_factory;

    /**
     * CacheMiddleware constructor.
     * @param \Concrete\Core\Application\Application $application
     * @param \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory $request_factory
     * @param \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory $response_factory
     */
    public function __construct(Application $application, HttpFoundationFactory $request_factory, DiactorosFactory $response_factory)
    {
        $this->setApplication($application);

        $this->response_factory = $response_factory;
        $this->request_factory = $request_factory;
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
        switch ($this->getDirection()) {
            case $this::DIRECTION_IN:
                return $this->checkSendCache($request, $response, $next);
            case $this::DIRECTION_OUT:
                return $this->handleStorage($request, $response, $next);
        }
    }

    /**
     * Send the cached response at the begining of the request if we have one
     */
    public function checkSendCache($request, $response, \Closure $next)
    {
        $app = $this->getApplication();
        if ($app instanceof Application) {
            $legacy_request = $this->request_factory->createRequest($request);
            if ($cache_response = $this->getApplication()->checkPageCache($legacy_request)) {
                dd($cache_response);
                return $this->response_factory->createResponse($cache_response);
            }
        }

        dd('not cached');
        return $next($request, $response);
    }

    public function handleStorage($request, $response, \Closure $next)
    {
        // Handle storing things on the response to the cache
        return $next($request, $response);
    }

}
