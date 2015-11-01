<?php
namespace Concrete\Core\Http;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\RequestHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Kernel implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @type RequestHandler */
    protected $requestHandler;

    /** @type \Concrete\Core\Http\RequestDispatcherInterface */
    protected $dispatcher;

    /**
     * Kernel constructor.
     * @param \Concrete\Core\Application\Application $application
     * @param \Concrete\Core\Http\Middleware\RequestHandlerInterface $requestHandler
     * @param \Concrete\Core\Http\RequestDispatcherInterface $dispatcher
     */
    public function __construct(
        Application $application,
        RequestHandlerInterface $requestHandler,
        RequestDispatcherInterface $dispatcher)
    {
        $this->setApplication($application);
        $this->requestHandler = $requestHandler;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return RequestHandler
     */
    public function getRequestHandler()
    {
        return $this->requestHandler;
    }

    /**
     * @param RequestHandler $requestHandler
     */
    public function setRequestHandler($requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @return RequestDispatcher
     */
    public function getRequestDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param RequestDispatcher $dispatcher
     */
    public function setRequestDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


    /**
     * Handle a request
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handleRequest(RequestInterface $request, ResponseInterface $response)
    {
        $kernel = $this;
        return $this->requestHandler->handleRequest($request, $response, function($request, $response) use ($kernel) {
            return $kernel->dispatch($request, $response);
        });
    }

    /**
     * Dispatch a request
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array [RequestInterface, ResponseInterface]
     */
    private function dispatch(RequestInterface $request, ResponseInterface $response)
    {
        return $dispatcher_response = $this->dispatcher->dispatch($request, $response);
    }

}
