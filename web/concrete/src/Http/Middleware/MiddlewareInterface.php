<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\Middleware\Pipeline\RequestPipeInterface;

interface MiddlewareInterface extends RequestPipeInterface
{

    /** No direction, this is used if we're the kernel  */
    const DIRECTION_NONE = 0;

    /** The initial direction, in */
    const DIRECTION_IN = 1;

    /** The secondary direction, out */
    const DIRECTION_OUT = 2;

    /**
     * Set the middleware direction, in or out.
     *
     * @param $direction MiddlewareInterface::[DIRECTION_IN | DIRECTION_OUT]
     * @return void
     */
    public function setDirection($direction);

}
