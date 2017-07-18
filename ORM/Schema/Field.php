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
     * @var array
     */
    protected $ormSettings = array();

    /**
     * Field constructor.
     * @param string $type
     * @param bool $searchable
     * @param bool $required
     * @param string $fieldGroup
     */
    public function __construct($type, $searchable, $required, $fieldGroup, $infoCollector, $position)
    {
        $this->searchable = $searchable;
        $this->required = $required;
        $this->type = $type;
        $this->fieldGroup = $fieldGroup;
        $this->infoCollector = $infoCollector;
        $this->position = $position;
    }


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

    /**
     * @return array
     */
    public function getOrmSettings()
    {
        return $this->ormSettings;
    }

    /**
     * @param array $ormSettings
     */
    public function setOrmSettings(array $ormSettings)
    {
        $this->ormSettings = $ormSettings;
    }

    public function getOrmSetting($name, $default = false)
    {
        return array_key_exists($name, $this->ormSettings) ? $this->ormSettings[$name] : $default;
    }
}