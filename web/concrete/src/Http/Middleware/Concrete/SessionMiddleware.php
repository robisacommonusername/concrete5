<?php
namespace Concrete\Core\Http\Middleware\Concrete;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Http\Middleware\MiddlewareTrait;
use Concrete\Core\Permission\IPService;
use Concrete\Core\Utility\IPAddress;
use Illuminate\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

class SessionMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @type \Symfony\Component\HttpFoundation\Session\Session */
    protected $session;

    /** @type \Concrete\Core\Permission\IPService */
    protected $ip_helper;

    /**
     * SessionMiddleware constructor.
     * @param \Illuminate\Container\Container $application
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @param \Concrete\Core\Permission\IPService $ip_helper
     */
    public function __construct(Container $application, SymfonySession $session, IPService $ip_helper)
    {
        $this->setApplication($application);
        $this->session = $session;
        $this->ip_helper = $ip_helper;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        // Start the session
        $this->beginSession($request, $response);

        // Set the session to the request
        $request = $request->withAttribute('session', $this->session);
        list($request, $response) = $next($request, $response);

        // End the session
        $this->endSession($request, $response);

        return [$request, $response];
    }

    /**
     * End the session
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $next($request, $response);
     */
    private function endSession(ServerRequestInterface $request, ResponseInterface $response)
    {
        // Save the session stored against the request
        $session = $request->getAttribute('session');
        if ($session && $session instanceof SymfonySession) {
            $session->save();
        }
    }

    /**
     * Enable session
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return mixed
     */
    private function beginSession(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->session->start();
        $this->testSessionFixation($this->session);
    }

    /**
     * Test the session for fixation
     * @param $session
     */
    private function testSessionFixation($session)
    {
        $currentIp = $this->ip_helper->getRequestIP();

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
