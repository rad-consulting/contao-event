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
     * @var array
     */
    protected $listeners = array();

    /**
     * @var array
     */
    protected $sorted = array();

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
                    $event->run()->save();

                    foreach ($this->getListeners($event) as $listener) {
                        System::log(json_encode($listener), __METHOD__, TL_ERROR);

                        try {
                            call_user_func_array($listener, array($event, $event->getName(), $this));
                        }
                        catch (Exception $e) {
                            $event->wait($e)->save();
                        }
                    }

                    // $event->delete();
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function addListener($event, $listener, $priority = 0)
    {
        System::log(json_encode(array($event, $listener, $priority)), __METHOD__, TL_ERROR);

        $this->listeners[$event][$priority][] = $listener;
        unset($this->sorted[$event]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getListeners(Event $event = null)
    {
        if (null !== $event) {
            $event = $event->getName();

            if (!isset($this->listeners[$event])) {
                return array();
            }

            if (!isset($this->sorted[$event])) {
                $this->sortListeners($event);
            }

            return $this->sorted[$event];
        }

        foreach ($this->listeners as $event => $listeners) {
            if (!isset($this->sorted[$event])) {
                $this->sortListeners($event);
            }
        }

        return array_filter($this->sorted);
    }

    /**
     * @inheritdoc
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $event => $params) {
            if (is_string($params)) {
                $this->addListener($event, array($subscriber, $params));
            }
            elseif (is_string($params[0])) {
                $this->addListener($event, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            }
            else {
                foreach ($params as $listener) {
                    $this->addListener($event, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $event => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->removeListener($event, array($subscriber, $listener[0]));
                }
            }
            else {
                $this->removeListener($event, array($subscriber, is_string($params) ? $params : $params[0]));
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getListenerPriority($event, $listener)
    {
        if (!isset($this->listeners[$event])) {
            return null;
        }

        foreach ($this->listeners[$event] as $priority => $listeners) {
            if (false !== in_array($listener, $listeners, true)) {
                return $priority;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function hasListeners($event = null)
    {
        return (bool)count($this->getListeners($event));
    }

    /**
     * @inheritdoc
     */
    public function removeListener($event, $listener)
    {
        if (!isset($this->listeners[$event])) {
            return $this;
        }

        foreach ($this->listeners[$event] as $priority => $listeners) {
            if (false !== ($key = array_search($listener, $listeners, true))) {
                unset($this->listeners[$event][$priority][$key], $this->sorted[$event]);
            }
        }

        return $this;
    }

    /**
     * @param string $event
     * @return void
     */
    protected function sortListeners($event)
    {
        krsort($this->listeners[$event]);
        $this->sorted[$event] = call_user_func_array('array_merge', $this->listeners[$event]);
    }
}
