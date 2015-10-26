<?php
namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Illuminate\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

class SessionMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use MiddlewareTrait, ApplicationAwareTrait;

    /** @type \Symfony\Component\HttpFoundation\Session\Session */
    protected $session;

    /**
     * SessionMiddleware constructor.
     * @param \Illuminate\Container\Container $application
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     */
    public function __construct(Container $application, SymfonySession $session)
    {
        $this->setApplication($application);
        $this->session = $session;
    }

    /**
     * Handle a request and a response
     * This method will either return $next($request, $response); or will create and return an error response like a 404
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Closure $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handleRequest(ServerRequestInterface $request, ResponseInterface $response, \Closure $next)
    {
        switch ($this->getDirection()) {
            case $this::DIRECTION_IN:
                return $this->beginSession($request, $response, $next);
            case $this::DIRECTION_OUT:
                return $this->endSession($request, $response, $next);
        }

        throw new \RuntimeException('This middleware can only be executed on the way in or the way out.');
    }

    /**
     * End the session
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Closure $next
     * @return \Psr\Http\Message\ResponseInterface $next($request, $response);
     */
    protected function endSession(ServerRequestInterface $request, ResponseInterface $response, \Closure $next)
    {
        $this->session->save();

        return $next($request, $response);
    }

    /**
     * Enable session
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Closure $next
     * @return mixed
     */
    protected function beginSession(ServerRequestInterface $request, ResponseInterface $response, \Closure $next)
    {
        dd('session');
        $this->session->start();
        $request = $request->withAttribute('session', $this->session);
        $this->testSessionFixation($this->session);

        return $next($request, $response);
    }


    protected function testSessionFixation($session)
    {
        $iph = Core::make('helper/validation/ip');
        $currentIp = $iph->getRequestIP();

        $ip = $session->get('CLIENT_REMOTE_ADDR');
        $agent = $session->get('CLIENT_HTTP_USER_AGENT');
        if ($ip && $ip != $currentIp->getIp(IPAddress::FORMAT_IP_STRING) || $agent && $agent != $_SERVER['HTTP_USER_AGENT']) {
            $session->invalidate();
        }
        if (!$ip && $currentIp !== false) {
            $session->set('CLIENT_REMOTE_ADDR', $currentIp->getIp(IPAddress::FORMAT_IP_STRING));
        }
        if (!$agent && isset($_SERVER['HTTP_USER_AGENT'])) {
            $session->set('CLIENT_HTTP_USER_AGENT', $_SERVER['HTTP_USER_AGENT']);
        }
    }

}
