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


use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Nitronet\eZORMBundle\ORM\Schema\SchemasManager;
use Nitronet\eZORMBundle\ORM\SchemaInterface;
use \SplObjectStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Registry implements \Countable, \IteratorAggregate
{
    /**
     * Storage handler
     *
     * @var SplObjectStorage
     */
    protected $store;

    /**
     * @var PropertyAccessor
     */
    protected static $accessor;

    /**
     * Registry Constructor
     */
    public function __construct()
    {
        $this->store            = new SplObjectStorage();
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
     * @return PropertyAccessor
     */
    public static function getAccessor()
    {
        if (!isset(static::$accessor)) {
            static::$accessor = PropertyAccess::createPropertyAccessorBuilder()
                ->enableMagicCall()
                ->getPropertyAccessor()
            ;
        }

        return static::$accessor;
    }

    /**
     * Stores an object into registry
     *
     * @param mixed $object
     * @param ContentInfo $contentInfo
     * @param SchemaInterface|null $schema
     * @param int $state
     * @param null $language
     * @param array $data
     *
     * @return Entry
     */
    public function store($object, ContentInfo $contentInfo = null, SchemaInterface $schema = null,
        $state = RegistryState::UNKNOWN, $language = null, array $data = array()
    ) {
        if ($this->has($object)) {
            return $this->getEntry($object);
        }

        $entry = Entry::factory($object, $contentInfo, $schema, $state, $language, $data);
        if ($object instanceof EventSubscriberInterface) {
            $entry->addSubscriber($object);
        }

        $this->store->attach($entry);

        return $entry;
    }

    /**
     * @param ContentInfo $contentInfo
     * @param string $className
     * @param null|string $language
     *
     * @return false|Entry
     */
    public function getEntryByContentInfo(ContentInfo $contentInfo, $className = null, $language = null)
    {
        foreach ($this->store as $entry) {
            /** @var Entry $entry */
            if ($entry->match($contentInfo, $className, $language)) {
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
    public function free()
    {
        unset($this->store);
        $this->store        = new SplObjectStorage();

        return $this;
    }
}