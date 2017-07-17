<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Schema\Builder;


use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Manager\FieldsManager;
use Nitronet\eZORMBundle\ORM\Schema\Field;
use Nitronet\eZORMBundle\ORM\Schema\Schema;
use Nitronet\eZORMBundle\ORM\SchemaInterface;

class ContentTypeSchemaBuilder
{
    /**
     * @var ContentType
     */
    protected $contentType;

    /**
     * @var FieldsManager
     */
    protected $fieldsManager;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * ContentTypeSchemaBuilder constructor.
     * @param ContentType $contentType
     * @param FieldsManager $fieldsManager
     * @param Connection $connection
     */
    public function __construct(ContentType $contentType, FieldsManager $fieldsManager, Connection $connection)
    {
        $this->contentType      = $contentType;
        $this->fieldsManager    = $fieldsManager;
        $this->connection       = $connection;
    }

    /**
     * @return SchemaInterface
     */
    public function build()
    {
        $schema         = new Schema();
        foreach ($this->contentType->getFieldDefinitions() as $fieldDefinition) {
            $schema->addField($fieldDefinition->identifier, $this->buildFieldFromDefinition($fieldDefinition));
        }

        return $schema;
    }

    /**
     * @param FieldDefinition $fieldDefinition
     *
     * @return Field
     */
    protected function buildFieldFromDefinition(FieldDefinition $fieldDefinition)
    {
        $mainLangCode   = $this->contentType->mainLanguageCode;
        $helper         = $this->fieldsManager->loadFieldHelper($fieldDefinition->fieldTypeIdentifier);
        $field          = new Field(
            $fieldDefinition->fieldTypeIdentifier,
            $fieldDefinition->isSearchable,
            $fieldDefinition->isRequired,
            $fieldDefinition->fieldGroup,
            $fieldDefinition->isInfoCollector,
            $fieldDefinition->position
        );

        $field->setSettings($fieldDefinition->getFieldSettings());

        $descs = $fieldDefinition->getDescriptions();
        // eZ Bugfix: Warning: array_key_exists() expects parameter 2 to be array, boolean given
        if (is_array($descs)) {
            $field->setDescription($fieldDefinition->getDescription($mainLangCode));
        }
        $field->setRequired($fieldDefinition->isRequired);
        $field->setFieldHelper($helper);
        $field->setDefaultValue($helper->toEntityValue($fieldDefinition->defaultValue, $this->connection, $field));
        $field->setOrmSettings($helper->getDefaultORMSettings());

        return $field;
    }
}
