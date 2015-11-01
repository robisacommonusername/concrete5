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

    use ApplicationAwareTrait;

    /**
     * CacheMiddleware constructor.
     * @param \Concrete\Core\Application\Application $application
     */
    public function __construct(Application $application)
    {
        $this->setApplication($application);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $app = $this->getApplication();
        if ($this->getDirection() == $this::DIRECTION_IN && $app instanceof Application) {
            $app->handleAutomaticUpdates();
        }

        return $next($request, $response);
    }

}
