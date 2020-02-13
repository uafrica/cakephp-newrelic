<?php
declare(strict_types=1);

namespace NewRelic;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;

/**
 * Class Plugin
 */
class Plugin extends BasePlugin
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        if (!extension_loaded('newrelic')) {
            return;
        }

        $isCli = PHP_SAPI === 'cli';
        if (!defined('NEW_RELIC_APP_NAME')) {
            $appType = $isCli ? 'cli' : 'app';
            $appName = 'web';

            if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
                $appName = 'admin';
            }

            define('NEW_RELIC_APP_NAME', sprintf('%1$s - %2$s - %3$s', 'production', $appType, $appName));
        }

        newrelic_set_appname(NEW_RELIC_APP_NAME);
        newrelic_background_job($isCli);
        newrelic_capture_params(true);
    }
}
