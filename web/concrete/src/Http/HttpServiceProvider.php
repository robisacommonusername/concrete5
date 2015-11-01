<?php
namespace Concrete\Core\Http;
use Concrete\Core\Application\Application;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Middleware\Pipeline\MiddlewarePipeline;

class HttpServiceProvider extends ServiceProvider {

	public function register() {
		$app = $this->getApplication();
		$singletons = array(
			'helper/ajax' => '\Concrete\Core\Http\Service\Ajax',
			'helper/json' => '\Concrete\Core\Http\Service\Json'
		);

		foreach($singletons as $key => $value) {
			$app->singleton($key, $value);
		}

		$app->singleton('Concrete\Core\Http\RequestDispatcher');
		$app->bind('Concrete\Core\Http\RequestDispatcherInterface', 'Concrete\Core\Http\RequestDispatcher');
		$app->singleton('Concrete\Core\Http\RequestHandler');
		$app->bind('Concrete\Core\Http\RequestHandlerInterface', 'Concrete\Core\Http\RequestHandler');

		$app->bind(
			'Concrete\Core\Http\Middleware\Pipeline\RequestPipelineInterface',
			'Concrete\Core\Http\Middleware\Pipeline\MiddlewarePipeline');

		$app->bind(
				'Concrete\Core\Http\Middleware\RequestHandlerInterface',
				'Concrete\Core\Http\Middleware\RequestHandler');

		$app->singleton('Concrete\Core\Http\Middleware\RequestHandler');

		$app->bind('Concrete\Core\Http\RequestHandler', function(Application $app) {
			$handler = new RequestHandler($app->make('\Concrete\Core\Http\Middleware\Pipeline\MiddlewarePipeline'));

			// Add middlewares
			$middlewares = $app['config']->get('http.middleware');
			foreach ($middlewares as $middleware) {
				list($class, $priority) = $middleware;
				$handler->addMiddleware($app->make($class), $priority);
			}

			return $handler;
		});
	}


}
