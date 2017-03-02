<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Registry;


use \SplObjectStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Registry implements \Countable, \IteratorAggregate
{
    const ACTION_SAVE           = 'save';
    const ACTION_DELETE         = 'delete';

    /**
     * Storage handler
     *
     * @var SplObjectStorage
     */
    protected $store;

    /**
     * @var integer
     */
    protected $_priority    = \PHP_INT_MAX;

    /**
     * Registry Constructor
     */
    public function __construct()
    {
        $this->store        = new SplObjectStorage();
    }

    /**
     * Tells if the registry contains an instance of the object
     *
     * @param mixed $object
     *
     * @return boolean
     */
    public function has($object)
    {
        return false !== $this->getEntry($object);
    }

    /**
     *
     * @param mixed $object
     *
     * @return Entry|false
     */
    public function getEntry($object)
    {
        foreach ($this->store as $entry) {
            /** @var Entry $entry */
            if ($entry->matchObject($object)) {
                return $entry;
            }
        }

        return false;
    }


    /**
     * Stores an object into registry
     *
     * @param mixed $object
     * @param array $identifiers
     * @param int $state
     * @param array $data
     *
     * @return Entry
     */
    public function store($object, array $identifiers = array(), $state = RegistryState::UNKNOWN, array $data = array())
    {
        if ($this->has($object)) {
            return $this->getEntry($object);
        }

        $entry = Entry::factory($object, $identifiers, $state, $data);
//        $dispatcher = $entry->data('dispatcher', new Dispatcher());
        $listeners  = $entry->data('listeners', array());

        if ($object instanceof EventSubscriberInterface) {
//            foreach ($object->getListeners() as $key => $listener) {
//                if (is_object($listener) && !is_callable($listener)) {
//                    $dispatcher->addListener($listener);
//                } elseif (is_callable($listener)) {
//                    $dispatcher->on($key, $listener);
//                }
//            }
        }

        foreach ($listeners as $key => $listener) {
//            if (is_object($listener) && !is_callable($listener)) {
//                $dispatcher->addListener($listener);
//            } elseif (is_callable($listener)) {
//                $dispatcher->on($key, $listener);
//            }
        }

        $this->store->attach($entry);

        return $entry;
    }

    /**
     * @param array $identifiers
     * @param string $className
     * 
     * @return Entry|false
     */
    protected function getEntryByIdentifiers(array $identifiers, $className = null)
    {
        foreach ($this->store as $entry) {
            /** @var Entry $entry */
            if ($entry->match($identifiers, $className)) {
                return $entry;
            }
        }

        return false;
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        $arr = array();
        foreach ($this->store as $entry) {
            $arr[] = $entry->getObject();
        }
        return $arr;
    }

    /**
     *
     * @return integer
     */
    public function count()
    {
        return count($this->store);
    }

    /**
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     *
     * @return Registry
     */
    public function clear()
    {
        unset($this->store);
        $this->store        = new SplObjectStorage();
        $this->_priority    = \PHP_INT_MAX;

        return $this;
    }
}