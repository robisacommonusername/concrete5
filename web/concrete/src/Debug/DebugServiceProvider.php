<?php
namespace Concrete\Core\Debug;


use Illuminate\Support\ServiceProvider;
use DebugBar\StandardDebugBar;

class DebugServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->instance('debugbar', $bar = new StandardDebugBar());
        $debugStack = new \Doctrine\DBAL\Logging\DebugStack();


        $db = $this->app->make('database');

        $db->getActiveConnection()->getConfiguration()->setSQLLogger($debugStack);
        $bar->addCollector(new \DebugBar\Bridge\DoctrineCollector($debugStack));

    }

}
