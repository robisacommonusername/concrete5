<?php

namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Concrete\Core\Url\Resolver\Manager\ResolverManager;
use Illuminate\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\RedirectResponse;

class InstallMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @type \Concrete\Core\Url\Resolver\Manager\ResolverManager */
    protected $url;

    /**
     * SessionMiddleware constructor.
     * @param \Illuminate\Container\Container $application
     */
    public function __construct(Container $application, ResolverManager $url_resolver)
    {
        $this->setApplication($application);
        $this->url = $url_resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $app = $this->getApplication();
        $is_install_path = substr($request->getUri()->getPath(), 0, 8) == '/install';

        if ($app->isInstalled() || $is_install_path) {
            return $next($request, $response);
        }

        return [$request, new RedirectResponse($this->url->resolve(['/install']))];
    }

}
