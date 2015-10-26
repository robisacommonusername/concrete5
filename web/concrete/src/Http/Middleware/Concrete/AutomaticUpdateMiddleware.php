<?php

namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AutomaticUpdateMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use MiddlewareTrait, ApplicationAwareTrait;

    /**
     * CacheMiddleware constructor.
     * @param \Concrete\Core\Application\Application $application
     */
    public function __construct(Application $application)
    {
        $this->setApplication($application);
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
        if ($this->getDirection() == $this::DIRECTION_IN && $app instanceof Application) {
            $app->handleAutomaticUpdates();
        }

        return $next($request, $response);
    }

}
