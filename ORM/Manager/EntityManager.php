<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Feb 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Manager;


use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Registry\Entry;
use Nitronet\eZORMBundle\ORM\Registry\Registry;
use Nitronet\eZORMBundle\ORM\Registry\RegistryState;
use Nitronet\eZORMBundle\ORM\SchemaInterface;
use Nitronet\eZORMBundle\ORM\WorkerInterface;

class EntityManager
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var
     */
    protected $registry;

    /**
     * @var \SplQueue
     */
    protected $workersQueue;

    /**
     * SchemasManager constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection   = $connection;
        $this->registry     = new Registry($connection->getSchemasManager());
        $this->workersQueue = new \SplQueue();
    }

    /**
     *
     * @param ContentInfo|null $contentInfo
     * @param SchemaInterface|null $schema
     * @param null|string $language
     *
     * @return object
     */
    public function entityFactory(ContentInfo $contentInfo = null, SchemaInterface $schema = null,
        $language = null
    ) {
        if (empty($language)) {
            $language = $this->connection->getDefaultLanguageCode();
        }

        if (null === $schema) {
            if ($contentInfo instanceof ContentInfo) {
                $schema = $this->connection->getSchemasManager()
                    ->loadSchemaByContentTypeId($contentInfo->contentTypeId);
            }
        }

        $entityClass = $schema->getEntityClass();
        $entry = false;
        if ($contentInfo instanceof ContentInfo) {
            $entry = $this->registry->getEntryByContentInfo($contentInfo, $entityClass, $language);
        }

        if (false === $entry) {
            $entry = $this->registry->store(
                new $entityClass,
                $contentInfo,
                $schema,
                RegistryState::REGISTERED,
                $language
            );
        }

        return $entry->getObject();
    }

    /**
     * @param object $entity
     * @param Location|Location[]|int|int[]|null $location
     * @param null|string $language
     * @param bool $draft
     * @param bool $visible
     */
    public function persist($entity, $location, $language = null, $draft = false, $visible = true)
    {
    }

    /**
     * @param object $entity
     * @param Location|Location[]|int|int[]|null $location
     */
    public function remove($entity, $location = null)
    {
    }

    /**
     *
     * @return void
     */
    public function flush()
    {
        foreach ($this->workersQueue as $worker) {
            if ($worker instanceof WorkerInterface) {
                $worker->execute($this->connection);
            }
        }
    }
}