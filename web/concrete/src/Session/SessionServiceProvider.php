<?php
namespace Concrete\Core\Session;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class SessionServiceProvider extends ServiceProvider
{

    /**
     * Binds "session" and "Symfony\Component\HttpFoundation\Session\Session"
     */
    public function register()
    {
        $this->app->bind('session', 'Symfony\Component\HttpFoundation\Session\Session');
        $this->app->bindShared('Symfony\Component\HttpFoundation\Session\Session', function(Application $app) {
            return Session::start();
        });
    }

    /**
     * A list of bindings that this provides to the application instance
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'session',
            'Symfony\Component\HttpFoundation\Session\Session'
        ];
    }

}
