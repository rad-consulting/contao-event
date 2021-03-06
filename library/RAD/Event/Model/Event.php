<?php
/**
 * @copyright  RAD Consulting GmbH 2017
 * @author     Chris Raidler <c.raidler@rad-consulting.ch>
 * @author     Olivier Dahinden <o.dahinden@rad-consulting.ch>
 */
namespace RAD\Event\Model;

use Exception;
use Contao\Model;
use RAD\Log\LogException;
use RAD\Log\Model\Log;

/**
 * Class Event
 *
 * @property int    $id
 * @property int    $pid
 * @property int    $tstamp
 * @property int    $attempt
 * @property int    $timeout
 * @property int    $error
 * @property int    $status
 * @property string $name
 * @property string $ptable
 * @property string $argument
 */
class Event extends Model
{
    /**
     * @const int
     */
    const WAITING = 0;
    const RUNNING = 1;

    /**
     * @var string
     */
    public static $strTable = 'tl_rad_event';

    /**
     * @param string     $name
     * @param Model      $subject
     * @param array|null $arguments
     * @param int        $timeout
     * @return static
     */
    public static function factory($name, Model $subject = null, array $arguments = null, $timeout = 295)
    {
        $instance = new static();
        $instance->name = $name;
        $instance->tstamp = time();
        $instance->status = static::WAITING;
        $instance->argument = $arguments ? serialize($arguments) : null;
        $instance->timeout = $timeout;

        if ($subject) {
            $instance->pid = $subject->id;
            $instance->ptable = $subject::getTable();
        }

        return $instance;
    }

    /**
     * @param string     $name
     * @param mixed|null $default
     * @return mixed|null
     */
    public function getArgument($name, $default = null)
    {
        $arguments = $this->getArguments();

        if (isset($arguments[$name]) && !empty($arguments[$name])) {
            return $arguments[$name];
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function setArgument($name, $value)
    {
        $arguments = $this->getArguments();
        $arguments[$name] = $value;
        $this->setArguments($arguments);

        return $this;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return deserialize($this->argument, true);
    }

    /**
     * @param array $arguments
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->argument = serialize($arguments);

        return $this;
    }

    /**
     * @return int
     */
    public function getAttempt()
    {
        return $this->attempt;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Model|null
     */
    public function getSubject()
    {
        if (!empty($this->pid) || !empty($this->ptable)) {
            $class = $GLOBALS['TL_MODELS'][$this->ptable];
            $subject = forward_static_call(array($class, 'findByPk'), $this->pid);

            if ($subject instanceof Model) {
                return $subject;
            }
        }

        return null;
    }

    /**
     * @param Model $subject
     * @return $this
     */
    public function setSubject(Model $subject)
    {
        $this->pid = $subject->id;
        $this->ptable = $subject::getTable();

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->tstamp;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return (bool)$this->error;
    }

    /**
     * @return $this
     */
    public function run()
    {
        $this->attempt++;
        $this->status = static::RUNNING;
        $this->tstamp = time();
        $this->error = 0;

        return $this;
    }

    /**
     * @param Exception|null $e
     * @return $this
     */
    public function wait(Exception $e = null)
    {
        $this->status = static::WAITING;

        if ($e) {
            $this->error = 1;
            $this->log($e, $e->getCode());
        }

        return $this;
    }

    /**
     * @param string|Exception $message
     * @param int              $level
     * @param string|null      $data
     * @return $this
     */
    public function log($message, $level = Log::INFO, $data = null)
    {
        if ($message instanceof LogException) {
            $data = $message->getData();
            $message = $message->getMessage();
        }
        else if ($message instanceof Exception) {
            $message = $message->getMessage();
        }

        Log::factory($this, $message, $level, $data)->save();

        return $this;
    }

    /**
     * @return int
     */
    public function delete()
    {
        Log::deleteByModel($this);

        return parent::delete();
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return static::RUNNING == $this->status;
    }
}
