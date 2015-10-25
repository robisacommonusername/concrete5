<?php
namespace Concrete\Core\Foundation\Service;
use \Concrete\Core\Application\Application;
use Closure;
use Concrete\Core\Application\ApplicationAwareInterface;
use Illuminate\Container\Container;

/**
 *  Extending this class allows groups of services to be registered at once.
 */
abstract class Provider implements ApplicationAwareInterface {

	public $app;

	public function __construct(Container $app)
	{
		$this->setApplication($app);
	}

	/**
	 * Set the application object
	 *
	 * @param \Illuminate\Container\Container $application
	 */
	public function setApplication(Container $application)
	{
		$this->app = $application;
	}

	/**
	 * Get the application object
	 *
	 * @return \Illuminate\Container\Container
	 */
	public function getApplication()
	{
		return $this->app;
	}

	/**
	 * Registers the services provided by this provider.
	 * @return void
	 */
	abstract public function register();

	/**
	 * A list of bindings that this provides to the application instance
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return [];
	}

}
