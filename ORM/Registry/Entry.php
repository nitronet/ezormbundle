<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Registry;

use \ArrayAccess;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use Nitronet\eZORMBundle\ORM\SchemaInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 */
class Entry extends EventDispatcher implements ArrayAccess
{
    /**
     * @var ContentInfo|null
     */
    protected $contentInfo = null;

    /**
     * @var null|SchemaInterface
     */
    protected $schema = null;

    /**
     * Registry state of the object
     *
     * @var int
     */
    protected $state = RegistryState::UNKNOWN;

    /**
     * Initial object values
     *
     * @var array
     */
    protected $initialValues = array();

    /**
     * @var integer
     */
    protected $storeTs = null;

    /**
     * @var integer
     */
    protected $stateTs = null;

    /**
     * The stored object
     *
     * @var mixed
     */
    protected $object;

    /**
     * Class name of the stored object
     *
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $language;

    /**
     * Extra data associated with the stored object
     *
     * @var array
     */
    protected $data = array();

    /**
     * Constructor
     *
     * @param object $object
     * @param ContentInfo $contentInfo
     * @param SchemaInterface|null $schema
     * @param int $state
     * @param array $data
     *
     * @internal param array $ids
     */
    public function __construct($object, ContentInfo $contentInfo = null, SchemaInterface $schema = null, $state = RegistryState::UNKNOWN, $language = null, array $data = array())
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('value is not an object');
        }


        $this->object       =& $object;
        $this->contentInfo  = $contentInfo;
        $this->schema       = $schema;
        $this->language     = $language;
        $this->storeTs      = time();
        $this->state        = $state;
        $this->stateTs      = time();
        $this->className    = get_class($object);
        $this->data         = $data;

        if ($this->isState(RegistryState::FRESH)) {
            // object stored as "fresh" -> define initial values
            $this->fresh();
        }
    }

    /**
     * State comparision
     *
     * @param integer $state
     *
     * @return boolean
     */
    public function isState($state)
    {
        return $this->state === $state;
    }

    /**
     * Changes current state
     *
     * @param integer $state
     *
     * @return Entry
     */
    public function setState($state)
    {
        $this->state    = $state;
        $this->stateTs  = time();

        return $this;
    }

    /**
     * Returns the state of the object
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Returns the date of the current state
     *
     * @return \DateTime
     */
    public function getStateDate()
    {
        $date = new \DateTime();
        $date->setTimestamp($this->stateTs);

        return $date;
    }

    /**
     * Returns the date of the storage
     *
     * @return \DateTime
     */
    public function getStoreDate()
    {
        $date = new \DateTime();
        $date->setTimestamp($this->storeTs);

        return $date;
    }

    /**
     * Defines initial values of the object and mark it as "fresh"
     *
     * @return Entry
     */
    public function fresh()
    {
          // TODO Change this
//        $accessor               = new Accessor($this->object);
//        $this->initialValues    = $accessor->toArray(array($accessor, 'everythingAsArrayModifier'));

        $this->setState(RegistryState::FRESH);

        return $this;
    }

    /**
     * Test this Entry against identifiers and className
     *
     * @param ContentInfo $contentInfo
     * @param string $className
     * @param null|string $language
     *
     * @return bool
     */
    public function match(ContentInfo $contentInfo, $className = null, $language = null)
    {
        if ($this->contentInfo instanceof ContentInfo && $contentInfo->id === $this->contentInfo->id) {
            if (null === $className || (null !== $className && $this->className === $className)) {
                return ($language === $this->language);
            }
            return false;
        }

        return false;
    }

    /**
     * Test this Entry against an object instance
     *
     * @param mixed $object
     *
     * @return boolean
     */
    public function matchObject($object)
    {
        return $this->object === $object;
    }

    /**
     * Tells if the objects values has been changed
     *
     * @return boolean
     */
    public function hasChanged()
    {
        if ($this->isState(RegistryState::CHANGED)) {
            return true;
        }

        if (!$this->isState(RegistryState::FRESH)) {
            return false;
        }

//        $accessor   = new Accessor($this->object);
//        $values     = $accessor->toArray(array($accessor, 'everythingAsArrayModifier'));
//        $diff       = array_diff_assoc($this->initialValues, $values);
        // TODO Change this
        $diff = array();

        if (!count($diff)) {
            return false;
        }

        $this->setState(RegistryState::CHANGED);

        return true;
    }

    /**
     * Returns an array of changed values
     *
     * @return array
     */
    public function getChangedValues()
    {
//        $accessor   = new Accessor($this->object);
//        $values     = $accessor->toArray(array($accessor, 'everythingAsArrayModifier'));
        // TODO Change this
        $values = array();

        $diff       = array();
        foreach ($values as $key => $val) {
            if(!isset($this->initialValues[$key]) || $this->initialValues[$key] !== $val) {
                $diff[$key] = $val;
            }
        }

        if (count($diff) && $this->isState(RegistryState::FRESH)) {
            $this->setState(RegistryState::CHANGED);
        }

        return $diff;
    }

    /**
     * Getter for associated data
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function data($key, $default = false)
    {
        if (!array_key_exists($key, $this->data) && $default != false) {
            $this->data[$key] = $default;
        }

        return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
    }

    /**
     * Merge datas with the existing ones
     *
     * @param array $data
     *
     * @return Entry
     */
    public function mergeData(array $data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Factory method
     *
     * @param mixed $object
     * @param ContentInfo $contentInfo
     * @param SchemaInterface $schema
     * @param int $state
     * @param null $language
     * @param array $data
     * @return Entry
     */
    public static function factory($object, ContentInfo $contentInfo = null, SchemaInterface $schema = null, $state = RegistryState::UNKNOWN, $language = null, array $data = array())
    {
        return new self($object, $contentInfo, $schema, $state, $language, $data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->data) ? $this->data[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Returns the stored object
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * String representation of the Entry
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this->object) . '@' . spl_object_hash($this->object);
    }

    /**
     * @return ContentInfo|null
     */
    public function getContentInfo()
    {
        return $this->contentInfo;
    }

    /**
     * @param ContentInfo|null $contentInfo
     */
    public function setContentInfo($contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    /**
     * @return SchemaInterface|null
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param SchemaInterface|null $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }
}