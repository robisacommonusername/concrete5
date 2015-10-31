<?php
namespace Concrete\Core\Http\Middleware\Pipeline;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class MiddlewarePipeline
 * @package Concrete\Core\Http
 */
class MiddlewarePipeline implements RequestPipelineInterface
{

    /** @type ServerRequestInterface */
    protected $request;

    /** @type ResponseInterface */
    protected $response;

    /** @type RequestPipeInterface[] */
    protected $pipes;

    /**
     * Set the request and the response to send
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return static
     */
    public function send(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;

        return $this;
    }

    /**
     * Set the pipes to send the request and response through
     *
     * @param  $pipes
     * @return static
     */
    public function through(array $pipes)
    {
        $this->pipes = $pipes;

        return $this;
    }

    /**
     * Run the pipeline with a final handler
     *
     * @param callable $then (\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
     *                       SHOULD return \Psr\Http\Message\ResponseInterface
     * @return ResponseInterface
     */
    public function then(callable $then)
    {
        $pipes = array_reverse($this->pipes);

        /** @type callable $linked_closure */
        $linked_closure = array_reduce($pipes, $this->getIterator(), $this->getInitial($then));

        return $linked_closure($this->request, $this->response);
    }

    /**
     * Get the iterator closure, this wraps every item in the list and injects the
     * @return callable
     */
    protected function getIterator()
    {
        return function (callable $next, callable $pipe) {
            return function (ServerRequestInterface $request, ResponseInterface $response) use ($next, $pipe) {
                return $pipe($request, $response, $next);
            };
        };
    }

    /**
     * The initial closure
     *
     * @param callable $then
     * @return callable
     */
    protected function getInitial(callable $then)
    {
        return function(ServerRequestInterface $request, ResponseInterface $response) use ($then) {
            return $then($request, $response);
        };
    }

}
