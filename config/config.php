<?php
/**
 * @copyright  RAD Consulting GmbH 2017
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @author     Olivier Dahinden <o.dahinden@rad-consulting.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

// Event
$GLOBALS['TL_MODELS'][\RAD\Event\Model\Event::getTable()] = 'RAD\\Event\\Model\\Event';
$GLOBALS['TL_CRON']['minutely'][] = array('RAD\\Event\\EventDispatcher', 'run');
$GLOBALS['RAD_LISTENERS'] = array();
$GLOBALS['RAD_SUBSCRIBERS'] = array();

$GLOBALS['BE_MOD']['system']['events'] = array(
    'tables' => array('tl_rad_event', 'tl_rad_log'),
    'icon' => 'system/themes/flexible/images/about.gif',
    'exec' => array('RAD\\Event\\Backend\\Command', 'executeEvent'),
);

$GLOBALS['RAD_LOG_ENTITIES']['events'] = array(
    'ptable' => 'tl_rad_event',
    'headerFields' => array('id', 'name', 'tstamp', 'attempt', 'timeout'),
);
