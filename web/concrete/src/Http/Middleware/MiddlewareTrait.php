<?php

namespace Concrete\Core\Http\Middleware;

trait MiddlewareTrait
{

    protected $middlewareDirection = MiddlewareInterface::DIRECTION_IN;

    /**
     * Set the middleware direction, in or out.
     *
     * @param $direction MiddlewareInterface::[DIRECTION_IN | DIRECTION_OUT]
     * @return void
     */
    public function setDirection($direction)
    {
        $this->middlewareDirection = $direction;
    }

    /**
     * Get the current middleware direction
     *
     * @return int
     */
    public function getDirection()
    {
        return $this->middlewareDirection;
    }

}
