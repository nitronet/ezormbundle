<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Schema;


use Nitronet\eZORMBundle\ORM\Exception\ORMException;
use Nitronet\eZORMBundle\ORM\SchemaInterface;

class Schema implements SchemaInterface
{
    /**
     * @var Field[]
     */
    protected $fields = array();

    /**
     * @var MetaFieldInterface[]
     */
    protected $metaFields = array();

    /**
     * @var string|null
     */
    protected $entityClass;

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return MetaFieldInterface[]
     */
    public function getMetaFields()
    {
        return $this->metaFields;
    }

    /**
     * @param string $attribute
     * @param Field $field
     *
     * @return Schema
     */
    public function addField($attribute, Field $field)
    {
        $this->fields[$attribute] = $field;

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return Field
     * @throws ORMException if the field is not registered
     */
    public function getField($attribute)
    {
        if (!array_key_exists($attribute, $this->fields)) {
            throw ORMException::unknownFieldFactory($attribute);
        }

        return $this->fields[$attribute];
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function hasField($attribute)
    {
        return array_key_exists($attribute, $this->fields);
    }


    /**
     * @param string $attribute
     * @param MetaFieldInterface $metaField
     *
     * @return Schema
     */
    public function addMetaField($attribute, MetaFieldInterface $metaField)
    {
        $this->metaFields[$attribute] = $metaField;

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return MetaFieldInterface
     * @throws ORMException if the metaField is not registered
     */
    public function getMetaField($attribute)
    {
        if (!array_key_exists($attribute, $this->metaFields)) {
            throw ORMException::unknownMetaFieldFactory($attribute);
        }

        return $this->metaFields[$attribute];
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function hasMetaField($attribute)
    {
        return array_key_exists($attribute, $this->metaFields);
    }

    /**
     * @return null|string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param null|string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }
}