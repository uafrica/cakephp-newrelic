<?php
declare(strict_types=1);

namespace NewRelic\Traits;

use Cake\Command\Command;
use Cake\Console\Shell;
use Cake\Http\ServerRequest;
use NewRelic\Lib\NewRelic;
use Throwable;

trait NewRelicTrait
{
    /**
     * The transaction name to use
     *
     * @var string
     */
    protected $_newrelicTransactionName;

    /**
     * Set the transaction name
     *
     * If `$name` is a Shell instance, the name will
     * automatically be derived based on best practices
     *
     * @param string|\Cake\Console\Shell|\Cake\Command\Command|\Cake\Http\ServerRequest $name String name or class to get name from
     * @return void
     */
    public function setName($name): void
    {
        if ($name instanceof Shell) {
            $name = $this->_deriveNameFromShell($name);
        }

        if ($name instanceof Command) {
            $name = $this->_deriveNameFromCommand($name);
        }

        if ($name instanceof ServerRequest) {
            $name = $this->_deriveNameFromRequest($name);
        }

        $this->_newrelicTransactionName = $name;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->_newrelicTransactionName;
    }

    /**
     * Change the application name
     *
     * @param  string $name Application name
     * @return void
     */
    public function applicationName(string $name): void
    {
        NewRelic::applicationName($name);
    }

    /**
     * Start a NewRelic transaction
     *
     * @param  null|string $name Start a transaction
     * @return void
     */
    public function start(?string $name = null): void
    {
        NewRelic::start($this->_getTransactionName($name));
    }

    /**
     * Stop a transaction
     *
     * @param bool $ignore Should the statistics NewRelic gathered be discarded?
     * @return void
     */
    public function stop(bool $ignore = false): void
    {
        NewRelic::stop($ignore);
    }

    /**
     * Ignore current transaction
     *
     * @return void
     */
    public function ignoreTransaction(): void
    {
        NewRelic::ignoreTransaction();
    }

    /**
     * Ignore current apdex
     *
     * @return void
     */
    public function ignoreApdex(): void
    {
        NewRelic::ignoreApdex();
    }

    /**
     * Add custom parameter to transaction
     *
     * @param  string $key Parameter name
     * @param  mixed $value Value for the parameter
     * @return void
     */
    public function parameter(string $key, $value): void
    {
        NewRelic::parameter($key, $value);
    }

    /**
     * Add custom metric
     *
     * @param  string $key Metric name
     * @param  float $value Value for the metric
     * @return void
     */
    public function metric(string $key, $value): void
    {
        NewRelic::metric($key, $value);
    }

    /**
     * capture params
     *
     * @param bool $capture Enable/Disable capturing of parameters
     * @return void
     */
    public function captureParams(bool $capture): void
    {
        NewRelic::captureParams($capture);
    }

    /**
     * Add custom tracer method
     *
     * @param string $method Name of the method to trace
     * @return void
     */
    public function addTracer(string $method): void
    {
        NewRelic::addTracer($method);
    }

    /**
     * Set user attributes
     *
     * @param  string $user Name or username for this transaction
     * @param  string $account Account name
     * @param  string $product A product name
     * @return void
     */
    public function user(string $user = '', string $account = '', string $product = ''): void
    {
        NewRelic::user($user, $account, $product);
    }

    /**
     * Send an exception to New Relic
     *
     * @param \Throwable $e A throwable to send to New Relic
     * @return void
     */
    public function sendException(Throwable $e): void
    {
        NewRelic::sendException($e);
    }

    /**
     * Get transaction name
     *
     * @param  string|null $name An override name to use
     * @return string
     */
    protected function _getTransactionName(?string $name): string
    {
        if (is_string($name)) {
            return $name;
        }

        return $this->_newrelicTransactionName;
    }

    /**
     * Derive the transaction name
     *
     * @param \Cake\Console\Shell $shell The shell instance
     * @return string
     */
    protected function _deriveNameFromShell(Shell $shell): string
    {
        $name = [];

        if ($shell->plugin) {
            $name[] = $shell->plugin;
        }

        $name[] = $shell->name;
        $name[] = $shell->command;

        return join('/', $name);
    }

    /**
     * Derive the transaction name
     *
     * @param \Cake\Console\Command $command The command instance
     * @return string
     */
    protected function _deriveNameFromCommand(Command $command): string
    {
        return $command->getName();
    }

    /**
     * Compute name based on request information
     *
     * @param \Cake\Http\ServerRequest $request The request object
     * @return string
     */
    protected function _deriveNameFromRequest(ServerRequest $request): string
    {
        $name = [];

        if ($request->getParam('plugin')) {
            $name[] = $request->getParam('plugin');
        }

        if ($request->getParam('prefix')) {
			$name[] = $request->getParam('prefix');
		}

        $name[] = $request->getParam('controller');
        $name[] = $request->getParam('action');

        $name = array_filter($name);
        if (empty($name)) {
            return (string)$request->getUri();
        }

        $name = join('/', $name);

        if ($request->getParam('ext')) {
            $name .= '.' . $request->getParam('ext');
        }

        return $name;
    }
}
