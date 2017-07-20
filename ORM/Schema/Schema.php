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
     * @var string
     */
    protected $contentTypeIdentifier;

    protected $contentTypeDescription;

    protected $contentTypeIsContainer;

    protected $contentTypeUrlAliasSchema;

    protected $contentTypeMainLanguageCode;

    /**
     * Schema constructor.
     * @param string $contentTypeIdentifier
     */
    public function __construct($contentTypeIdentifier)
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

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

    /**
     * @return string
     */
    public function getContentTypeIdentifier()
    {
        return $this->contentTypeIdentifier;
    }

    /**
     * @param string $contentTypeIdentifier
     */
    public function setContentTypeIdentifier($contentTypeIdentifier)
    {
        $this->contentTypeIdentifier = $contentTypeIdentifier;
    }

    /**
     * @return mixed
     */
    public function getContentTypeDescription()
    {
        return $this->contentTypeDescription;
    }

    /**
     * @param mixed $contentTypeDescription
     */
    public function setContentTypeDescription($contentTypeDescription)
    {
        $this->contentTypeDescription = $contentTypeDescription;
    }

    /**
     * @return mixed
     */
    public function getContentTypeIsContainer()
    {
        return $this->contentTypeIsContainer;
    }

    /**
     * @param mixed $contentTypeIsContainer
     */
    public function setContentTypeIsContainer($contentTypeIsContainer)
    {
        $this->contentTypeIsContainer = $contentTypeIsContainer;
    }

    /**
     * @return mixed
     */
    public function getContentTypeUrlAliasSchema()
    {
        return $this->contentTypeUrlAliasSchema;
    }

    /**
     * @param mixed $contentTypeUrlAliasSchema
     */
    public function setContentTypeUrlAliasSchema($contentTypeUrlAliasSchema)
    {
        $this->contentTypeUrlAliasSchema = $contentTypeUrlAliasSchema;
    }

    /**
     * @return mixed
     */
    public function getContentTypeMainLanguageCode()
    {
        return $this->contentTypeMainLanguageCode;
    }

    /**
     * @param mixed $contentTypeMainLanguageCode
     */
    public function setContentTypeMainLanguageCode($contentTypeMainLanguageCode)
    {
        $this->contentTypeMainLanguageCode = $contentTypeMainLanguageCode;
    }
}