<?php
/**
 * This file is part of the eZ ORMBundle Project
 *
 * @license BSD 3-clauses
 * @author Julien Ballestracci
 * @since Jul 2017
 * @version 1.0
 */
namespace Nitronet\eZORMBundle\ORM\Workers;


use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Events\AfterPublishEvent;
use Nitronet\eZORMBundle\ORM\Events\BeforePublishEvent;
use Nitronet\eZORMBundle\ORM\Exception\ORMException;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\Registry\Registry;
use Nitronet\eZORMBundle\ORM\Registry\RegistryState;
use Nitronet\eZORMBundle\ORM\Schema\Field;
use Nitronet\eZORMBundle\ORM\WorkerInterface;

class SaveEntityWorker implements WorkerInterface
{
    /**
     * @var object
     */
    protected $entity;

    /**
     * @var Location|Location[]|int|\int[]|string|\string[] $location
     */
    protected $location;

    /**
     * @var null|string
     */
    protected $language;

    /**
     * @var bool
     */
    protected $draft;

    /**
     * @var bool
     */
    protected $hidden;

    /**
     * SaveEntityWorker constructor.
     *
     * @param object $entity
     * @param Location|Location[]|int|\int[]|string|\string[] $location $location
     * @param null|string $language
     * @param bool $draft
     * @param bool $hidden
     */
    public function __construct($entity, $location = null, $language = null, $draft = false, $hidden = false)
    {
        $this->entity = $entity;
        $this->location = $location;
        $this->language = $language;
        $this->draft = $draft;
        $this->hidden = $hidden;
    }

    /**
     * @param Connection $connection
     *
     * @return mixed
     * @throws ORMException when entity is in an invalid registry state
     */
    public function execute(Connection $connection)
    {
        $registry   = $connection->getEntityManager()->getRegistry();
        $entry      = $registry->getEntry($this->entity);

        if (false === $entry) {
            throw ORMException::unregisteredEntityExceptionFactory($this->entity);
        }

        // another Worker is currently processing this entity, stop here.
        if ($entry->isWorking()) {
            return;
        }

        $state      = $entry->getState();
        $schema     = $entry->getSchema();
        switch($state)
        {
            case RegistryState::FRESH:
            case RegistryState::CHANGED:
                // Update
                $values = array();
                $changed = $entry->getChangedValues();
                foreach ($schema->getFields() as $key => $field) {
                    /** @var Field $field */
                    if (array_key_exists($key, $changed)) {
                        $values[$field->getIdentifier()] = $field
                            ->getFieldHelper()
                            ->toEzValue(
                                Registry::getAccessor()->getValue($entry->getObject(), $key
                                ), $connection);
                    }
                }

                if (!count($values)) {
                    return;
                }

                $entry->setWorking(true);

                $event = new BeforePublishEvent($connection, $entry->getObject());
                $entry->dispatch($event);

                if ($event->isPropagationStopped()) {
                    $entry->setWorking(false);
                    return;
                }

                $query = Query::factory()
                    ->update($entry->getContentInfo())
                    ->hidden($this->hidden)
                    ->draft($this->draft)
                    ->into($this->location)
                    ->values($values)
                ;

                $connection->execute($query, null, $this->language);

                break;

            case RegistryState::REGISTERED:
                // Insert
                $entry->setWorking(true);

                $event = new BeforePublishEvent($connection, $entry->getObject());
                $entry->dispatch($event);

                if ($event->isPropagationStopped()) {
                    $entry->setWorking(false);
                    return;
                }

                $values = array();
                foreach ($schema->getFields() as $key => $field) {
                    /** @var Field $field */
                    $values[$field->getIdentifier()] = $field
                        ->getFieldHelper()
                        ->toEzValue(
                            Registry::getAccessor()->getValue($entry->getObject(), $key
                        ), $connection);
                }

                $query = Query::factory()
                    ->insert($schema->getContentTypeIdentifier())
                    ->hidden($this->hidden)
                    ->draft($this->draft)
                    ->into($this->location)
                    ->values($values)
                ;

                $result = $connection->execute($query, null, $this->language);
                if ($result instanceof Content) {
                    $entry->setContentInfo($result->contentInfo);
                }

                break;

            default:
                throw ORMException::invalidEntityStateExceptionFactory($this->entity, $this);
        }

        $entry->setWorking(false);
        $entry->dispatch(new AfterPublishEvent($connection, $entry->getObject()));
        $entry->fresh();
    }
}