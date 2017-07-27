<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\QueryHandler\FetchType;


use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Exception\ORMException;
use Nitronet\eZORMBundle\ORM\QueryHandler\FetchTypeInterface;
use Nitronet\eZORMBundle\ORM\Registry\Registry;
use Nitronet\eZORMBundle\ORM\Schema\Field;
use Nitronet\eZORMBundle\ORM\Schema\MetaFieldInterface;
use Nitronet\eZORMBundle\ORM\SchemaInterface;

class ORMFetchType implements FetchTypeInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * ORMFetchType constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns ORM entities from a query result
     *
     * @param SearchResult $searchResult
     * @param string|null $language
     *
     * @return array
     * @throws ORMException when using multiple languages
     */
    public function transform(SearchResult $searchResult, $language = null)
    {
        if (null !== $language && !is_string($language)) {
            throw ORMException::unsupportedMultiplesLanguagesFactory();
        }

        $results    = array();
        $em         = $this->connection->getEntityManager();
        $sm         = $this->connection->getSchemasManager();

        foreach ($searchResult->searchHits as $searchHit) {
            if (!$searchHit->valueObject instanceof Content) {
                continue;
            }

            $content        = $searchHit->valueObject;
            $matchedLang    = $searchHit->matchedTranslation;
            $schema         = $sm ->loadSchemaByContentTypeId($content->contentInfo->contentTypeId);

            $entity         = $em->entityFactory($content->contentInfo, $schema, $matchedLang);
            $results[]      = $this->populate($entity, $content, $schema, $language, $matchedLang);

            // mark entity as "fresh" from this point
            $em->getRegistry()->getEntry($entity)->fresh();
        }

        return $results;
    }

    /**
     * @param object  $entity
     * @param Content $content
     * @param SchemaInterface $schema
     * @param string $lang
     * @param string $defaultLang
     *
     * @return object
     */
    protected function populate($entity, Content $content, SchemaInterface $schema,
        $lang, $defaultLang
    ) {
        $pa = Registry::getAccessor();

        $fields = $schema->getFields();

        if (null !== $lang) {
            $langFields = $content->getFieldsByLanguage($lang);
        }

        foreach ($fields as $name => $field) {
            /** @var Field $field */

            $baseValue = (isset($langFields) && array_key_exists($name, $langFields) ?
                $langFields[$name]->value :
                $content->getFieldValue($name, $defaultLang)
            );

            $value = $field->getFieldHelper()->toEntityValue($baseValue, $this->connection, $field);
            if ($entity instanceof \stdClass) {
                $entity->{$name} = $value;
            } else {
                $pa->setValue($entity, $name, $value);
            }
        }

        $metaFields = $schema->getMetaFields();
        foreach ($metaFields as $name => $metaField) {
            /** @var MetaFieldInterface $metaField */

            $value = $metaField->toEntityValue($entity, $content, $metaField);
            if ($entity instanceof \stdClass) {
                $entity->{$name} = $value;
            } else {
                $pa->setValue($entity, $name, $value);
            }
        }

        return $entity;
    }
}