<?php
namespace Concrete\Core\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RequestDispatcherInterface
{

    /**
     * Dispatch a request
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array [ServerRequestInterface $request, ResponseInterface $response]
     */
    public function dispatch(ServerRequestInterface $request, ResponseInterface $response);

}
