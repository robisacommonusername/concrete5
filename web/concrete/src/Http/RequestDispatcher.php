<?php
namespace Concrete\Core\Http;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RequestDispatcher implements ApplicationAwareInterface, RequestDispatcherInterface
{

    use ApplicationAwareTrait;

    /** @type \Symfony\Component\EventDispatcher\EventDispatcher */
    protected $eventDispatcher;

    /**
     * RequestDispatcher constructor.
     * @param \Concrete\Core\Application\Application $application
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     */
    public function __construct(Application $application, EventDispatcher $dispatcher)
    {
        $this->setApplication($application);
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * Dispatch a request and a response
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array [ServerRequestInterface $request, ResponseInterface $response]
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response)
    {
        $app = $this->getApplication();

        if ($app instanceof Application) {
            $legacy_factory = $app->make('Concrete\Core\Http\Factory\ConcreteRequestFactory');
            $psr_factory = $app->make('Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory');

            $symfony_request = $legacy_factory->createRequest($request);

            Request::setInstance($symfony_request);
            $this->eventDispatcher->dispatch('on_before_dispatch');

            $dispatcher_response = $app->dispatch($symfony_request);

            $response = $psr_factory->createResponse($dispatcher_response);
        }

        return [$request, $response];
    }
}
