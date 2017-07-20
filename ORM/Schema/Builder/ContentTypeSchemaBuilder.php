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
use Nitronet\eZORMBundle\ORM\Schema\MetaField\ContentId;
use Nitronet\eZORMBundle\ORM\Schema\MetaField\MainLanguageCode;
use Nitronet\eZORMBundle\ORM\Schema\MetaField\MainLocationId;
use Nitronet\eZORMBundle\ORM\Schema\MetaField\ModificationDate;
use Nitronet\eZORMBundle\ORM\Schema\MetaField\PublishedDate;
use Nitronet\eZORMBundle\ORM\Schema\MetaField\RemoteId;
use Nitronet\eZORMBundle\ORM\Schema\MetaField\SectionId;
use Nitronet\eZORMBundle\ORM\Schema\MetaField\Version;
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
        $schema         = new Schema($this->contentType->identifier);

        $schema->setContentTypeDescription($this->contentType->getDescription($this->contentType->mainLanguageCode));
        $schema->setContentTypeIsContainer((bool)$this->contentType->isContainer);
        $schema->setContentTypeMainLanguageCode($this->contentType->mainLanguageCode);
        $schema->setContentTypeUrlAliasSchema($this->contentType->urlAliasSchema);

        foreach ($this->contentType->getFieldDefinitions() as $fieldDefinition) {
            $schema->addField($fieldDefinition->identifier, $this->buildFieldFromDefinition($fieldDefinition));
        }

        // add basic metaFields
        $schema->addMetaField(ContentId::DEFAULT_ATTR_NAME, new ContentId());
        $schema->addMetaField(MainLocationId::DEFAULT_ATTR_NAME, new MainLocationId());
        $schema->addMetaField(Version::DEFAULT_ATTR_NAME, new Version());
        $schema->addMetaField(PublishedDate::DEFAULT_ATTR_NAME, new PublishedDate());
        $schema->addMetaField(ModificationDate::DEFAULT_ATTR_NAME, new ModificationDate());
        $schema->addMetaField(RemoteId::DEFAULT_ATTR_NAME, new RemoteId());
        $schema->addMetaField(SectionId::DEFAULT_ATTR_NAME, new SectionId());
        $schema->addMetaField(MainLanguageCode::DEFAULT_ATTR_NAME, new MainLanguageCode());

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
            $fieldDefinition->identifier,
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
