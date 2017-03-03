<?php
/**
 * Contao extension for RAD Consulting GmbH
 *
 * @copyright  RAD Consulting GmbH 2016
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

// Buttons
$GLOBALS['TL_LANG']['tl_rad_event']['new'] = 'Neues Ereignis';

// Legends
$GLOBALS['TL_LANG']['tl_rad_event']['event_legend'] = 'Ereignis';

// Fields
$GLOBALS['TL_LANG']['tl_rad_event']['id'] = array('ID', 'Interne ID des Ereignisses.');
$GLOBALS['TL_LANG']['tl_rad_event']['name'] = array('Event', 'Name des Ereignisses.');
$GLOBALS['TL_LANG']['tl_rad_event']['pid'] = array('Subject ID', 'ID des Subjekts.');
$GLOBALS['TL_LANG']['tl_rad_event']['ptable'] = array('Subject', 'Tabelle des Subjekts.');
$GLOBALS['TL_LANG']['tl_rad_event']['tstamp'] = array('Zeitstempel');
$GLOBALS['TL_LANG']['tl_rad_event']['attempt'] = array('Versuch');
$GLOBALS['TL_LANG']['tl_rad_event']['timeout'] = array('Timeout', 'Dauer die das Ereignis nach seiner letzten Ausführung pausiert wird.');
$GLOBALS['TL_LANG']['tl_rad_event']['status'] = array('Status');
$GLOBALS['TL_LANG']['tl_rad_event']['argument'] = array('Arguments');
$GLOBALS['TL_LANG']['tl_rad_event']['error'] = array('Error');

// References
$GLOBALS['TL_LANG']['tl_rad_event']['status.reference'] = array(
    0 => 'Warten',
    1 => 'Wird ausgeführt',
);
