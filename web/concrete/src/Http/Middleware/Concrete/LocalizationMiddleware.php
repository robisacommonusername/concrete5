<?php
namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Concrete\Core\Localization\Localization;
use Concrete\Core\User\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LocalizationMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * CacheMiddleware constructor.
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
        $u = new User();
        $lan = $u->getUserLanguageToDisplay();
        $loc = Localization::getInstance();
        $loc->setLocale($lan);

        return $next($request, $response);
    }

}
