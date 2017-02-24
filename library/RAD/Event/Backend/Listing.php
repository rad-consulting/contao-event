<?php
/**
 * @copyright  RAD Consulting GmbH 2017
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @author     Olivier Dahinden <o.dahinden@rad-consulting.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
namespace RAD\Event\Backend;

use Contao\Backend;
use Contao\DataContainer;

/**
 * Class Listing
 */
class Listing extends Backend
{
    /**
     * @param array         $row
     * @param string        $label
     * @param DataContainer $dc
     * @param array         $args
     * @return array
     */
    public function listEvent(array &$row, $label, DataContainer $dc, array &$args)
    {
        $args[1] = date('Y-m-d H:i:s', $row['tstamp']);
        $args[3] = '#' . $row['attempt'];
        $args[4] = $row['timeout'] . 's';
        $args[6] = empty($row['pid']) ? '-' : $row['ptable'] . '#' . $row['pid'];

        return $args;
    }
}
