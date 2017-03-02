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


class Field
{
    /**
     * @var FieldHelperInterface
     */
    protected $fieldHelper;

    /**
     * @var bool
     */
    protected $searchable = true;

    /**
     * @var bool
     */
    protected $infoCollector = true;

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $fieldGroup = 'content';

    /**
     * @var bool
     */
    protected $translatable = true;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @return FieldHelperInterface
     */
    public function getFieldHelper()
    {
        return $this->fieldHelper;
    }

    /**
     * @param FieldHelperInterface $fieldHelper
     */
    public function setFieldHelper($fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;
    }

    /**
     * @return bool
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @param bool $searchable
     */
    public function setSearchable($searchable)
    {
        $this->searchable = $searchable;
    }

    /**
     * @return bool
     */
    public function isInfoCollector()
    {
        return $this->infoCollector;
    }

    /**
     * @param bool $infoCollector
     */
    public function setInfoCollector($infoCollector)
    {
        $this->infoCollector = $infoCollector;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getFieldGroup()
    {
        return $this->fieldGroup;
    }

    /**
     * @param string $fieldGroup
     */
    public function setFieldGroup($fieldGroup)
    {
        $this->fieldGroup = $fieldGroup;
    }

    /**
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->translatable;
    }

    /**
     * @param bool $translatable
     */
    public function setTranslatable($translatable)
    {
        $this->translatable = $translatable;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}