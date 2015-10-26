<?php
namespace Concrete\Core\Http;
use Concrete\Core\Application\Application;
use \Concrete\Core\Foundation\Service\Provider as ServiceProvider;

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

		$app->bind(
			'Concrete\Core\Http\Middleware\Pipeline\RequestPipelineInterface',
			'Concrete\Core\Http\Middleware\Pipeline\MiddlewarePipeline');

		$app->bind(
				'Concrete\Core\Http\Middleware\RequestHandlerInterface',
				'Concrete\Core\Http\Middleware\RequestHandler');

		$app->singleton('Concrete\Core\Http\Middleware\RequestHandler');
	}


}
