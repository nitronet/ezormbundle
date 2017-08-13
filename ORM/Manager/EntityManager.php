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
use Nitronet\eZORMBundle\ORM\Registry\Registry;
use Nitronet\eZORMBundle\ORM\Registry\RegistryState;
use Nitronet\eZORMBundle\ORM\SchemaInterface;
use Nitronet\eZORMBundle\ORM\WorkerInterface;
use Nitronet\eZORMBundle\ORM\Workers\RemoveEntityWorker;
use Nitronet\eZORMBundle\ORM\Workers\SaveEntityWorker;

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

        if ($schema instanceof SchemaInterface) {
            $entityClass = (!empty($schema->getEntityClass()) ?  $schema->getEntityClass() : \stdClass::class);
        } else {
            $entityClass = \stdClass::class;
        }

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
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @param object $entity
     * @param Location|Location[]|int|int[]|null $location
     * @param null|string $language
     * @param bool $draft
     * @param bool $hidden
     *
     * @return EntityManager
     */
    public function publish($entity, $location = null, $language = null, $draft = false, $hidden = false)
    {
        $this->workersQueue->push(new SaveEntityWorker($entity, $location, $language, $draft, $hidden));

        return $this;
    }

    /**
     * @param object $entity
     *
     * @return EntityManager
     */
    public function remove($entity)
    {
        $this->workersQueue->push(new RemoveEntityWorker($entity));

        return $this;
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

        unset($this->workersQueue);
        $this->workersQueue = new \SplQueue();
    }
}