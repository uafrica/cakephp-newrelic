<?php
declare(strict_types=1);

namespace NewRelic\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\EventInterface;
use NewRelic\Traits\NewRelicTrait;

/**
 * New Relic Component
 *
 * @author Christian Winther
 */
class NewRelicComponent extends Component
{
    use NewRelicTrait;

    /**
     * Called before the Controller::beforeFilter().
     *
     * Start NewRelic and configure transaction name
     *
     * @param \Cake\Event\EventInterface $event The event to use
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        $this->setName($this->getController()->getRequest());
        $this->start();

        $controller = $this->getController();
        if (isset($controller->Auth)) {
            $this->user((string)$controller->Auth->user('id'), (string)$controller->Auth->user('email'), '');
        }

        $this->captureParams(true);
    }
}
