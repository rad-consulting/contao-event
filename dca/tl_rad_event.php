<?php
/**
 * Contao extension for RAD Consulting GmbH
 *
 * @copyright  RAD Consulting GmbH 2016
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

// Config
$GLOBALS['TL_DCA']['tl_rad_event']['config'] = array(
    'dataContainer' => 'Table',
    'closed' => true,
    'sql' => array(
        'keys' => array(
            'id' => 'primary',
            'name,subject' => 'unique',
        ),
    ),
);

// List
$GLOBALS['TL_DCA']['tl_rad_event']['list'] = array(
    'sorting' => array(
        'mode' => 2,
        'fields' => array('id ASC'),
        'panelLayout' => 'filter;sort,search,limit',
        'flag' => 12,
    ),
    'label' => array(
        'fields' => array('id', 'tstamp', 'status', 'attempt', 'timeout', 'name', 'subject', 'error'),
        'showColumns' => true,
        'label_callback' => array('RAD\\Event\\Backend\\Listing', 'listEvent'),
    ),
    'global_operations' => array(
        'back' => array(
            'label' => &$GLOBALS['TL_LANG']['MSC']['backBT'],
            'href' => 'mod=&table=',
            'class' => 'header_back',
            'attributes' => 'onclick="Backend.getScrollOffset();"',
        ),
        'all' => array(
            'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
            'href' => 'act=select',
            'class' => 'header_edit_all',
            'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
        ),
    ),
    'operations' => array(
        'log' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['log'],
            'href' => 'table=tl_g4g_log',
            'icon' => 'news.gif',
            'button_callback' => array('RAD\\Logging\\Backend\\Button', 'forLog'),
        ),
        'delete' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['delete'],
            'href' => 'act=delete',
            'icon' => 'delete.gif',
            'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
        ),
        'show' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['show'],
            'href' => 'act=show',
            'icon' => 'show.gif',
        ),
        'exec' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['exec'],
            'href' => 'key=exec',
            'icon' => 'manager.gif',
            'button_callback' => array('RAD\\Event\\Backend\\Button', 'forExecute'),
        ),
    ),
);

// Fields
$GLOBALS['TL_DCA']['tl_rad_event']['fields'] = array(
    'id' => array(
        'sql' => "int(10) unsigned NOT NULL auto_increment",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['id'],
    ),
    'tstamp' => array(
        'sql' => "int(10) unsigned NOT NULL default '0'",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['tstamp'],
    ),
    'timeout' => array(
        'sql' => "int(10) unsigned NOT NULL default '0'",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['timeout'],
    ),
    'attempt' => array(
        'sql' => "int(10) unsigned NOT NULL default '0'",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['attempt'],
    ),
    'name' => array(
        'sql' => "char(64) NOT NULL default ''",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['name'],
    ),
    'subject' => array(
        'sql' => "char(255) NOT NULL default ''",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['subject'],
    ),
    'argument' => array(
        'sql' => "char(255) NOT NULL default ''",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['argument'],
    ),
    'error' => array(
        'sql' => "char(1) NOT NULL default ''",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['error'],
    ),
    'status' => array(
        'sql' => "char(1) NOT NULL default ''",
        'label' => &$GLOBALS['TL_LANG']['tl_rad_event']['status'],
    ),
);
