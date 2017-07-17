<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jan 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Manager;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Nitronet\eZORMBundle\eZORMBundle;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Exception\ORMException;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\Schema\Builder\ContentTypeSchemaBuilder;
use Nitronet\eZORMBundle\ORM\Schema\Schema;
use Nitronet\eZORMBundle\ORM\SchemaInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SchemasManager implements ContainerAwareInterface
{
    /**
     * @var Schema[]
     */
    protected $schemasById = array();

    /**
     * @var Schema[]
     */
    protected $schemasByIdentifier = array();

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * SchemasManager constructor.
     *
     * @param ContainerInterface $container
     * @param Connection $connection
     */
    public function __construct(ContainerInterface $container, Connection $connection)
    {
        $this->container  = $container;
        $this->connection = $connection;
    }

    /**
     * @param integer $id
     *
     * @return SchemaInterface
     */
    public function loadSchemaByContentTypeId($id)
    {
        if (!array_key_exists($id, $this->schemasById)) {
            $query          = Query::factory()->select()->from(eZORMBundle::CONTENTTYPE_TABLE_ALIAS)->text($id);
            $results        = $this->connection->execute($query);
            $contentType    = $results[$id];
            $schema         = $this->loadSchema($contentType);

            $this->schemasById[$id] = $this->schemasByIdentifier[$contentType->identifier] = $schema;
        }

        return $this->schemasById[$id];
    }

    /**
     * @param ContentType $contentType
     *
     * @return SchemaInterface
     *
     * @throws ORMException when a loaded schema doesn't implement SchemaInterface
     */
    protected function loadSchema(ContentType $contentType)
    {
        $serviceId = 'ezorm.schema.'. $contentType->identifier;
        if ($this->container->has($serviceId)) {
            $schema = $this->container->get($serviceId);
        } else {
            $fieldsManager = $this->container->get(eZORMBundle::SERVICE_FIELD_MANAGER);
            $schema = (new ContentTypeSchemaBuilder($contentType, $fieldsManager, $this->connection))->build();
            $this->container->set($serviceId, $schema);
        }

        if (!$schema instanceof SchemaInterface) {
            throw ORMException::invalidSchemaImplFactory($schema, $serviceId);
        }

        return $schema;
    }

    /**
     * Defines the Dependency Injection Container
     *
     * @param ContainerInterface|null $container
     *
     * @return SchemasManager
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }
}