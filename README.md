# CakePHP <3 NewRelic

You can modify your files like this to have full NewRelic support.

This is a fork of https://github.com/jippi/cakephp-newrelic

## Things included

- NewRelic.NewRelic task
- NewRelic.NewRelic component
- NewRelicTrait trait
- NewRelic.NewRelic

## Requirements
- [New Relic PHP agent](https://docs.newrelic.com/docs/agents/php-agent/getting-started/introduction-new-relic-php) installed as a PHP module

## Installation

First require the NewRelic plugin.

```
composer require uafrica/cakephp-newrelic
```

Then load the plugin

```
bin/cake plugin load NewRelic
```

### Shell

Shells have been deprecated since CakePHP 3.6, but are still available for use. Include this snippet in `src/Shell/AppShell.php` and ensure all your shells extend it.

```php
	public function startup() {
		$this->NewRelic = $this->Tasks->load('NewRelic.NewRelic');
		$this->NewRelic->setName($this);
		$this->NewRelic->start();
		$this->NewRelic->parameter('params', json_encode($this->params));
		$this->NewRelic->parameter('args', json_encode($this->args));

		parent::startup();
	}
```

### Commands

Commands unfortunately do not offer a simple way to "inject" code into all commands. A trait is offered to handle the NewRelic injection.

Example usage:

```php
<?php

namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use NewRelic\Traits\NewRelicTrait;

class ChannelsImportCommand extends Command 
{
	use NewRelicTrait;

	public function execute(Arguments $args, ConsoleIo $io)
	{
		$this->setName($this);
		$this->setArguments($args);

		//Rest of your command code.
	}
}
```

### Controller

Simply add `NewRelic.NewRelic` to your `$components` list

### webroot/index.php

Add this in top of your file after `require dirname(__DIR__) . '/vendor/autoload.php';`

```php
if (extension_loaded('newrelic')) {
	$appType = 'app';
	$appName = 'web';

	if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
		$appName = 'admin';
	}

	define('NEW_RELIC_APP_NAME', sprintf('%1$s - %2$s - %3$s', 'production', $appType, $appName));

	newrelic_set_appname(NEW_RELIC_APP_NAME);
	newrelic_background_job(false);
	newrelic_capture_params(true);
}

// Rest of your index.php here
```

### bin/cake.php

Add this in top of your file after `require dirname(__DIR__) . '/vendor/autoload.php';`

```php
if (extension_loaded('newrelic')) {
	define('NEW_RELIC_APP_NAME', sprintf('%s - app - cli', 'production'));
	newrelic_set_appname(NEW_RELIC_APP_NAME);
	newrelic_background_job(true);
	newrelic_capture_params(true);
}

// Rest of your cake.php file here
```

### Middleware

You can use the supplied `NewRelicErrorHandlerMiddleware` placed in `NewRelic\Middleware\NewRelicErrorHandlerMiddleware` which extends the built in `Cake\Error\Middleware\ErrorHandlerMiddleware`. By using this you'll get the NewRelic working *and* have default CakePHP behavior.

Example:

```php
<?php

namespace App;

use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * Setup the middleware your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware.
     */
    public function middleware($middleware)
    {
        $middleware
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(\NewRelic\Middleware\NewRelicErrorHandlerMiddleware::class)
            // Handle plugin/theme assets like CakePHP normally does.
            ->add(AssetMiddleware::class)
            // Apply routing
            ->add(RoutingMiddleware::class);
	    
        return $middleware;
    }
}

?>
```
