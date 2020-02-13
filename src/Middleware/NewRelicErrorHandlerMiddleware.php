<?php
declare(strict_types=1);

namespace NewRelic\Middleware;

use Cake\Error\Middleware\ErrorHandlerMiddleware;
use NewRelic\Lib\NewRelic;

/**
 * {@inheritDoc}
 */
class NewRelicErrorHandlerMiddleware extends ErrorHandlerMiddleware
{
    /**
     * {@inheritDoc}
     */
    public function handleException($exception, $request, $response)
    {
        NewRelic::collect();
        NewRelic::sendException($exception);

        return parent::handleException($exception, $request, $response);
    }
}
