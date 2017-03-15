<?php
/**
 * @copyright  RAD Consulting GmbH 2017
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @author     Olivier Dahinden <o.dahinden@rad-consulting.ch>
 */
namespace RAD\Event;

/**
 * Interface EventSubscriberInterface
 */
interface EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents();
}
