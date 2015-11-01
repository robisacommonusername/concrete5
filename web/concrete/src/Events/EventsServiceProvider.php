<?php
namespace Concrete\Core\Events;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class EventsServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('Symfony\Component\EventDispatcher\EventDispatcher');
        $this->app->bind('director', 'Symfony\Component\EventDispatcher\EventDispatcher');
    }

}
