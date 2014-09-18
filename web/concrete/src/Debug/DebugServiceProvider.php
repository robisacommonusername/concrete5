<?php
namespace Concrete\Core\Debug;


use DebugBar\StandardDebugBar;
use Illuminate\Support\ServiceProvider;

class DebugServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->instance('debugbar', $bar = new StandardDebugBar());
        $debugStack = new \Doctrine\DBAL\Logging\DebugStack();

        // Cache javascript renderer object.
        $bar->getJavascriptRenderer('/concrete/vendor/maximebf/debugbar/src/DebugBar/Resources');

        \Database::connection()->getConfiguration()->setSQLLogger($debugStack);
        $bar->addCollector(new \DebugBar\Bridge\DoctrineCollector($debugStack));
    }

}
