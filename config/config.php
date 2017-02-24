<?php
/**
 * @copyright  RAD Consulting GmbH 2017
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @author     Olivier Dahinden <o.dahinden@rad-consulting.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


// Event
$GLOBALS['TL_MODELS'][\RAD\Event\Model\EventModel::getTable()] = 'RAD\\Event\\Model\\EventModel';
$GLOBALS['TL_CRON']['minutely'][] = array('RAD\\Event\\EventDispatcher', 'run');
$GLOBALS['RAD_LISTENERS'] = array();
$GLOBALS['RAD_SUBSCRIBERS'] = array();

$GLOBALS['BE_MOD']['system']['events'] = array(
    'tables' => array('tl_g4g_event', 'tl_g4g_log'),
    'icon' => 'system/themes/flexible/images/about.gif',
    'exec' => array('RAD\\Event\\Backend\\Command', 'executeEvent'),
);
