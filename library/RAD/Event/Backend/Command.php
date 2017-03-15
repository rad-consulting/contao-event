<?php
/**
 * @copyright  RAD Consulting GmbH 2017
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @author     Olivier Dahinden <o.dahinden@rad-consulting.ch>
 */
namespace RAD\Event\Backend;

use Contao\Backend;
use Contao\Controller;
use Contao\DC_Table;
use RAD\Event\Model\Event as Event;
use Haste\Input\Input;

/**
 * Class Command
 */
class Command extends Backend
{
    /**
     * @param DC_Table $dc
     * @return void
     */
    public function executeEvent(DC_Table $dc)
    {
        $db = $this->Database;
        $stmt = $db->prepare('UPDATE ' . Event::getTable() . ' SET `tstamp` = UNIX_TIMESTAMP() - `timeout` WHERE `id` = ? LIMIT 1');
        $stmt->execute(Input::get('id'));
        Controller::redirect('contao/main.php?do=events');
    }
}
