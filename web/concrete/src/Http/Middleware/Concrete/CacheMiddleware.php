<?php

namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Factory\ConcreteRequestFactory;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class CacheMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @type \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory */
    protected $request_factory;

    /** @type \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory */
    protected $response_factory;

    /**
     * CacheMiddleware constructor.
     * @param \Concrete\Core\Application\Application $application
     * @param \Concrete\Core\Http\Factory\ConcreteRequestFactory $request_factory
     * @param \Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory $response_factory
     */
    public function __construct(Application $application, ConcreteRequestFactory $request_factory, DiactorosFactory $response_factory)
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
     * @param callable $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // If we have a cache entry for this request, lets make a response and return it.
        if ($cached_response = $this->getCacheResponse($request, $response)) {
            return [$request, $cached_response];
        }
        list($request, $response) = $next($request, $response);

        // on the way out, lets check to see if we can cache the response against the request.
        $this->handleStorage($request, $response);

        return [$request, $response];
    }

    /**
     * Send the cached response at the begining of the request if we have one
     */
    private function getCacheResponse($request, $response)
    {
        $app = $this->getApplication();
        if ($app instanceof Application) {
            $legacy_request = $this->request_factory->createRequest($request);
            if ($cache_response = $this->getApplication()->checkPageCache($legacy_request)) {
                return $this->response_factory->createResponse($cache_response);
            }
        }
    }

    private function handleStorage($request, $response)
    {
        // Handle storing things on the response to the cache
    }

}
