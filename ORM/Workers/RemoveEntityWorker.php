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


use Nitronet\eZORMBundle\ORM\Connection;
use Nitronet\eZORMBundle\ORM\Events\AfterDeleteEvent;
use Nitronet\eZORMBundle\ORM\Events\BeforeDeleteEvent;
use Nitronet\eZORMBundle\ORM\Exception\ORMException;
use Nitronet\eZORMBundle\ORM\Query;
use Nitronet\eZORMBundle\ORM\Registry\RegistryState;
use Nitronet\eZORMBundle\ORM\WorkerInterface;

class RemoveEntityWorker implements WorkerInterface
{
    protected $entity;

    /**
     * @var null|string
     */
    protected $language;

    /**
     * RemoveEntityWorker constructor.
     * @param object $entity
     * @param string|null $language
     */
    public function __construct($entity, $language = null)
    {
        $this->entity = $entity;
        $this->language = $language;
    }

    /**
     *
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
        switch($state)
        {
            case RegistryState::FRESH:
            case RegistryState::CHANGED:
                // Delete
                $entry->setWorking(true);

                $event = new BeforeDeleteEvent($connection, $entry->getObject());
                $entry->dispatch($event);

                if ($event->isPropagationStopped()) {
                    $entry->setWorking(false);
                    return;
                }

                $query = Query::factory()->delete($entry->getContentInfo());
                $connection->execute($query, null, $this->language);
                break;

            case RegistryState::REGISTERED:
                return;

            default:
                throw ORMException::invalidEntityStateExceptionFactory($this->entity, $this);
        }

        $entry->setWorking(false);
        $entry->dispatch(new AfterDeleteEvent($connection, $entry->getObject()));

        $registry->removeEntry($entry);
    }
}