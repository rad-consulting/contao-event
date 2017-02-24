<?php
/**
 * @copyright  RAD Consulting GmbH 2017
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @author     Olivier Dahinden <o.dahinden@rad-consulting.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
namespace RAD\Event;

use Exception;
use Contao\Model;
use Contao\System;
use RAD\Event\Model\EventModel as Event;

/**
 * Class EventDispatcher
 */
class EventDispatcher
{
    /**
     * @var EventDispatcher
     */
    protected static $instance;

    /**
     * @return EventDispatcher
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * EventDispatcher constructor is protected to force singleton.
     */
    protected function __construct()
    {
        // Attach subscribers
        foreach ($GLOBALS['RAD_SUBSCRIBERS'] as $subscriber) {
            $subscriber = System::importStatic($subscriber);

            if ($subscriber instanceof EventSubscriberInterface) {
                $this->addSubscriber($subscriber);
            }
        }

        // Attach listeners
    }

    /**
     * @param Event $event
     * @return callable[]
     */
    public function getListeners(Event $event)
    {
        $name = $event->getName();

        // TODO
        return array();
    }

    /**
     * @return $this
     */
    public function addListener()
    {
        // TODO

        return $this;
    }

    /**
     * @param EventSubscriberInterface $subscriber
     * @return $this
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        // TODO

        return $this;
    }

    /**
     * @param Event|string $event
     * @param Model|null   $subject
     * @param array|null   $arguments
     * @param int          $timeout
     * @return $this
     */
    public function dispatch($event, Model $subject = null, array $arguments = null, $timeout = 295)
    {
        if (is_string($event)) {
            $event = Event::factory($event, $subject, $arguments, $timeout);
        }

        if ($event instanceof Event) {
            $event->save();
        }

        return $this;
    }

    /**
     * @return void
     */
    public function run()
    {
        $events = Event::findAll(array('order' => 'tstamp ASC', 'return' => 'Collection'));

        if ($events instanceof Model\Collection && $events->count()) {
            foreach ($events as $event) {
                if ($event instanceof Event && 0 == $event->getAttempt() || $event->getTimestamp() + $event->getTimeout() < time()) {
                    foreach ($this->getListeners($event) as $listener) {
                        try {
                            $event->run()->save();
                            call_user_func_array($listener, array($event, $event->getName(), $this));
                        }
                        catch (Exception $e) {
                            $event->wait($e)->save();
                        }
                    }
                }
            }
        }
    }
}
