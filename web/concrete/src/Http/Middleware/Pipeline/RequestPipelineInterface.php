<?php
namespace Concrete\Core\Http\Middleware\Pipeline;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface RequestPipelineInterface
 * A pipeline for bootstrapping a request
 * @package Concrete\Core\Http\Middleware\Pipeline
 */
interface RequestPipelineInterface
{

    /**
     * Set the request and the response to send
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @return static
     */
    public function send(ServerRequestInterface $request, ResponseInterface $response);

    /**
     * Set the pipes to send the request and response through
     *
     * @param PipeInterface[] $pipes
     * @return static
     */
    public function through(array $pipes);

    /**
     * Run the pipeline with a final handler
     *
     * @param \Closure $then (\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
     *                       SHOULD return \Psr\Http\Message\ResponseInterface
     * @return ResponseInterface
     */
    public function then(\Closure $then);

}
